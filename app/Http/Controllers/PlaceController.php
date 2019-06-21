<?php

namespace App\Http\Controllers;

use App\GooglePlaceCache;
use App\Http\Requests\AddNewPlaceRequest;
use App\Http\Requests\GetRandomPlaceRequest;
use App\Http\Requests\MissingPlaceSuggestionRequest;
use App\Http\Requests\PlaceDescriptionSuggestionRequest;
use App\Http\Requests\PlaceOwnershipRequest;
use App\Http\Requests\PlaceSearchRequest;
use App\Http\Requests\UserEditPlaceRequest;
use App\MissingPlaceSuggestion;
use App\Place;
use App\PlaceDescriptionSuggestion;
use App\PlaceTag;
use App\PlaceUpdateLog;
use App\Tag;
use App\UserFavorite;
use App\UserPlaceOwnershipClaim;
use App\UserSavedForLater;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PlaceController extends Controller
{
//


    /**
     * Get's a random place from the database, with a variety of possible parameters (Such as distance, if it's open, etc).
     * This could fail and lead to a race condition (due to the way distance, repetition, and open/not open works) because
     * we can call this function recursively to re-try randomization if the first attempt fails. The $iterator variables prevents
     * a race condition by force the function to return data to the user after a certain number of tries, even if it is not within
     * the user-defined parameters
     *
     * TODO: Need to clean this up to use SQL queries for distance and hours (is open/not open). However, we need to remove
     * the reliance of Google Places API first and save our hours to the database via a PlaceHours object.
     *
     * @param GetRandomPlaceRequest $request
     * @param int $iterator
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getRandomPlace(GetRandomPlaceRequest $request,$iterator = 0)
    {
        if($iterator >5)
        {
            //if we've got here, we're border on a race condition, so just sent an empty response.
            return response('Could not find a random place close to you.',404);
        }
        $useRadius=false;
        $radius = 40;//default fail-safe value
        $requires_open = empty($request['is_open']) ? false :(boolean)$request['is_open'];

        //get the reruns from the session, if they are there
        $runs = session('runs',[]);//retrieve the runs array from the session, or else initialize the empty array.

        if(!empty($request['lat']) && !empty($request['lng']) )
        {
            //browser gps coordinates were supplied, so add them to the session.
            session([
                'lat'=>$request['lat'],
                'lng'=>$request['lng']
            ]);

            if(!empty($request['distance']))
            {
                //translates a radius from the frontend to an actual max mileage
                //as a side effect, it protects against SQL injection when running the raw query
                $useRadius = true;
                switch($request['distance'])
                {
                    case 1:
                        $radius=1;
                        break;
                    case 2:
                        $radius=3;
                        break;
                    case 3:
                        $radius=5;
                        break;
                    case 4:
                        $radius=10;
                        break;
                    case 5:
                        $radius=20;
                        break;
                    default:
                        $useRadius = false;
                }//switch
            }//if
        }//if lat lng


        $place = Place::select('id','name','address','city','state_code','summary','google_place_id','latitude','longitude');

        if($useRadius)
        {
            $haversine = "(6371 * acos(cos(radians(" . $request['lat'] . ")) 
                    * cos(radians(`latitude`)) 
                    * cos(radians(`longitude`) 
                    - radians(" . $request['lng'] . ")) 
                    + sin(radians(" . $request['lat'] . ")) 
                    * sin(radians(`latitude`))))";

            $place = $place->selectRaw("{$haversine} AS distance")
                ->whereRaw("{$haversine} < ?", [$radius]);
        }

        $place = $place->whereNotIn('id',$runs);//prevent duplicates in randomization
        $place = $place->inRandomOrder()
            ->first();



        if(!$place)
        {//we couldn't get a place
            //first thing to look at is if or $runs is too full
            if(count($runs)>0)
            {//remove the runs restriction for now
                $runs = [];
                session(['runs'=>$runs]);//save to session
                $iterator++;
                return $this->getRandomPlace($request,$iterator);
            }
            else
            {//nothing in the runs array--so lets try upping the distance restriction
                $request['distance'] = $request['distance']+1;
                $iterator++;
                return $this->getRandomPlace($request,$iterator);
            }
        }
        $place = $place->append(['is_open','user_distance']);

        //to prevent a r rerun, save the id to the session so that we ignore it in the future
        $runs[] = $place->id;
        //if the rerun array exceeds 25, clear out the first element
        if(count($runs)>25)
        {
            array_shift($runs);//removes the first item
        }
        session(['runs'=>$runs]);//save to session
        //
        //final preflight; TODO: consider adding a scheduled task to update open status in database so that we can query it directly
        if(($requires_open && ($place->is_open==false))&& $iterator<5)
        {
            //this place must be open, but it's not, so try another place
            $iterator++;
            return $this->getRandomPlace($request,$iterator);
        }//the place is required to be open but it is not open
        else
        {
            //either this place is good to go, or we couldn't find a place after 5 iterations.
            //either way, we need to send something to the user, even if it isn't perfect
            return $place;
        }//else

    }//function getRandomPlaces


    /**
     * Returns a place object by ID
     * @param $place int the ID of the place
     * @return array
     */
    public function index($place)
    {

        $place = Place::where('id',$place)
            ->with('tags')
            ->first
            ();

        $place->append('user_distance');
        if(!empty($_GET['lat']) && !empty($_GET['lng']) )
        {
            //browser gps coordinates were supplied, so add them to the session.
            session([
                'lat'=>$_GET['lat'],
                'lng'=>$_GET['lng']
            ]);

        }

        //attempt to make a call to the Google API to build place information
        if (empty($place->google_place_id))
        {
            $place =$this->buildPlaceInformation($place);

        }
        $return = $place->toArray();
        $return['map_link']=$place->map_link;


        //see how old the information we have in the database is. Looks for a cache less than a week old
        $cached =GooglePlaceCache::where([
            ['google_place_id',$place->google_place_id],
            ['updated_at', '>=', \Carbon\Carbon::now()->subWeek()]
        ])->first();


        if($cached)
        {//if our cache is less than a week old, we can go ahead and use it
            $response = json_decode($cached->cached_content);
            $return['hours']=!empty($response->result->opening_hours->weekday_text) ? $response->result->opening_hours->weekday_text:[];
            $return['is_open']=$place->is_open;//since this information is cached, use our function to determine if they are open
            $return['price']=!empty($response->result->price_level) ? $response->result->price_level:1;
            $return['rating']=!empty($response->result->rating) ? $response->result->rating:5;
            $return['reviews']=!empty($response->result->reviews) ? $response->result->reviews:null;


        }
        else
        {//if the cache is over a week old, or we don't have a cache at all, make an API call and update the place object with the API info
            $key = config('app.PLACES_API_KEY');
            $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place->google_place_id}&fields=opening_hours,rating,price_level,reviews&key={$key}";
            if($response=@json_decode(@file_get_contents($url))) {
                $return['hours']=!empty($response->result->opening_hours->weekday_text) ? $response->result->opening_hours->weekday_text:[];
                $return['is_open']=!empty($response->result->opening_hours->open_now) ? $response->result->opening_hours->open_now:false;//since we are getting an API call right off the bat, don't worry about using our function to build the hours/determine if open
                $return['price']=!empty($response->result->price_level) ? $response->result->price_level:1;
                $return['rating']=!empty($response->result->rating) ? $response->result->rating :5;
                $return['reviews']=!empty($response->result->reviews) ? $response->result->reviews:null;

                GooglePlaceCache::updateOrCreate(
                    ['google_place_id'=>$place->google_place_id],
                    ['cached_content'=>json_encode($response)]
                );

            }//if response
        }//else
        return $return;
    }//function index

    /**
     * Get a  places that is owned by the currently logged-in user
     * @param $place
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function indexLocationOwnedByUser($place)
    {
        $place = Place::where('id',$place)->with('tags')->first();
        if($place->claim_status=='approved' ||$place->claim_status=='pending')
        {//only send the data if they have appropriate clearance (pending our approveD)
            return $place;
        }
        else
        {
            return response("You do not have access to edit {$place->name} .",422);
        }//else, they don't own this place
    }



    /**
     * Attempts to populate the place information using the Google Places API
     * @param Place $place
     * @return Place
     */
    public function buildPlaceInformation(Place $place)
    {
        $key = config('app.PLACES_API_KEY');
        $name=urlencode("$place->name Wichita");
        $url =   "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input={$name}&inputtype=textquery&locationbias=circle:20@37,-97&key={$key}";

        if($response=@json_decode(@file_get_contents($url))) {
            if($response->candidates)
            {
                foreach ($response->candidates as $candidate)
                {
                    $details =  $this->getPlaceDetails($candidate->place_id,$key);
                    if($details)
                   {
                       $place->address=$details['street_address'];
                       $place->city=$details['city'];
                       $place->state_code=$details['state'];
                       $place->phone_number=$details['phone'];
                       $place->google_place_id = $candidate->place_id;
                       $place->latitude=$details['lat'];
                       $place->longitude=$details['long'];
                       $place->name=$details['name'];
                       $place->save();
                       $place->refresh();
                       return $place;
                   }
                }//loop
            }//if response has candidates
        }//if json decodable response
        return $place;
    }//build place information


    /**
     * Gets the place details from a Google Places API call
     * @param $placeID
     * @param $key
     * @return array|bool
     */
    private function getPlaceDetails($placeID,$key)
    {
        $cached = GooglePlaceCache::where(
            'google_place_id',$placeID
        )->first();


        if($cached) {
            $response = json_decode($cached->cached_content);
        }
        else
        {
            $url="https://maps.googleapis.com/maps/api/place/details/json?placeid={$placeID}&key={$key}";
            $response=@json_decode(@file_get_contents($url));
            if($response)
            {
                //update the cache
                GooglePlaceCache::updateOrCreate(
                    ['google_place_id'=>$placeID],
                    ['cached_content'=>json_encode($response)]
                );
            }//if response
            else
            {
                return false;
            }
        }
        if($response->result) {
                //
                //get address details
                $place = $response->result;
                $address = $this->buildAddress($place->address_components);
                if($address['state']=='KS')
                {
                    //the state is KS, so this is likely a correct candidate, so set it up.
                    $address['phone'] = $res =!empty($place->formatted_phone_number )? preg_replace("/[^0-9]/", "", $place->formatted_phone_number ):null;
                    $address['lat']=$place->geometry->location->lat;
                    $address['long']=$place->geometry->location->lng;
                    $address['name']=$place->name;
                    return $address;
                }
            }
            return false;//default return
    }//getPlaceDetails


    /**
     * Builds a human-readable address from the Google Places API JSON
     * @param $address_components
     * @return array
     */
    private function buildAddress($address_components)
    {
        $addr = [
            'number'=>null,
            'street'=>null,
            'city'=>null,
            'state'=>null,
            'zip'=>null
        ];

        foreach ($address_components as $address_component) {
            //check if it is a street number
            if (in_array('street_number', $address_component->types))
            {
                $addr['number'] = $address_component->long_name;
            }//if

            //check if it is a street/route
            if (in_array('route', $address_component->types))
            {
                $addr['street'] = $address_component->short_name;
            }//if

            //check if it is a city
            if (in_array('locality', $address_component->types))
            {
                $addr['city'] = $address_component->long_name;
            }//if

            //check if it is a city
            if (in_array('administrative_area_level_1', $address_component->types))
            {
                $addr['state'] = $address_component->short_name;
            }//if

            //check if it is a postal code
            if (in_array('postal_code', $address_component->types))
            {
                $addr['zip'] = $address_component->short_name;
            }//if


        }//foreach

        $addr['street_address'] = "{$addr['number']} {$addr['street']}";

        return $addr;

    }//function buildAddress


    /**
     * Processes a user's suggestion from the frontend for adding a description for a place
     * @param PlaceDescriptionSuggestionRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function placeDescriptionSuggestion(PlaceDescriptionSuggestionRequest $request)
    {
        $suggestion = new PlaceDescriptionSuggestion();
        $suggestion->place_id = $request['id'];
        $suggestion->description = $request['description'];
        $suggestion->user_id = Auth::check() ? Auth::id() : null;
        $suggestion->save();
        return response(null,204);
    }

    /**
     * Processes a user's suggestion from the frontend for adding a place
     * @param MissingPlaceSuggestion $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function missingPlaceSuggestion(MissingPlaceSuggestionRequest $request)
    {
        $suggestion = new MissingPlaceSuggestion();
        $suggestion->description = $request['description'];
        $suggestion->user_id = Auth::check() ? Auth::id() : null;
        $suggestion->save();
        return response(null,204);
    }


    /**
     * Function to search (and paginate) the places in our database. Used by the Search.vue frontend of the page
     * @param PlaceSearchRequest $request
     * @return mixed
     */
    public function search(PlaceSearchRequest $request)
    {
        $useRadius = false;

        if(!empty($request['lat']) && !empty($request['lng']) )
        {
            //browser gps coordinates were supplied, so add them to the session.
            session([
                'lat'=>$request['lat'],
                'lng'=>$request['lng']
            ]);


            if(!empty($request['distance']))
            {//
                //translates a radius from the frontend to an actual max mileage
                //as a side effect, it protects against SQL injection when running the raw query
                $useRadius = true;
                switch($request['distance'])
                {
                    case 0:
                        $useRadius = false;
                        break;
                    case 1:
                        $radius=1;
                        break;
                    case 2:
                        $radius=3;
                        break;
                    case 3:
                        $radius=5;
                        break;
                    case 4:
                        $radius=10;
                        break;
                    case 5:
                        $radius=20;
                        break;
                    default:
                        $useRadius = false;
                }//switch
            }//if
        }//lat lng
        $places = Place::select('id','name','address','city','state_code','summary','google_place_id','latitude','longitude');

        $lat = !empty(session('lat')) ? session('lat'):37.6889;
        $lng = !empty(session('lng')) ? session('lng'):-97.336111;

        $userPoint = "point({$lng},{$lat})";
        $places->selectRaw("ST_Distance_Sphere(point(longitude,latitude), {$userPoint}) AS distance");
        if(!empty($request['name']))
        {
            $places->where('name','like',"%{$request['name']}%");

        }//search by name

        if(!empty($request['vegan']))
        {
            $places->where('has_vegan_options',true);

        }//search vegan places

        if(!empty($request['glutenFree']))
        {
            $places->where('has_gluten_free_options',true);

        }//search gluten-free places


       if(!empty($request['alcohol']))
        {
            $places->where('serves_alcohol',true);

        }//search places serving alcohol


       if(!empty($request['wifi']))
        {
            $places->where('has_public_wifi',true);

        }//search  places with wifi


       if(!empty($request['bikeRack']))
        {
            $places->where('has_bike_rack',true);

        }//search  places with a bike rack


       if(!empty($request['meals']))
        {
            $places->where('serves_full_meals',true);

        }//search  places with full meals
        //
        if(!empty($request['carryout']))
        {
            $places->where('has_carryout',true);

        }//search  places with carryout


        if(!empty($request['delivery']))
        {
            $places->where('has_delivery',true);

        }//search  places with delivery


        if(!empty($request['brunch']))
        {
            $places->where('serves_brunch',true);

        }//search  places with brunch

         if(!empty($request['charger']))
        {
            $places->where('has_ev_charger',true);

        }//search  places with ev charger

        if(!empty($request['parking']))
        {
            $places->where('has_free_parking',true);

        }//search  places with free parking
        if(!empty($request['tags']))
        {

            $tagged = PlaceTag::select('place_id')
                ->whereIn('tag_id',$request['tags'])
                ->get()
                ->pluck('place_id')
                ->toArray();

            $places->whereIn('id',$tagged);

        }//search  places with free parking

        if(!empty($request['favorited']))
        {
            $favorites = UserFavorite::where('user_id',Auth::id())
                ->get()
                ->pluck('place_id')
                ->toArray();
            $places->whereIn('id',$favorites);

        }//search  places the user added to favorites

        if(!empty($request['saved']))
        {
            $saved = UserSavedForLater::where('user_id',Auth::id())
                ->get()
                ->pluck('place_id')
                ->toArray();
            $places->whereIn('id',$saved);

        }//search  places the user added to saved for later list

        if($useRadius)
        {
            $haversine = "(6371 * acos(cos(radians(" . $request['lat'] . ")) 
                    * cos(radians(`latitude`)) 
                    * cos(radians(`longitude`) 
                    - radians(" . $request['lng'] . ")) 
                    + sin(radians(" . $request['lat'] . ")) 
                    * sin(radians(`latitude`))))";

            $places = $places->selectRaw("{$haversine} AS distance")
                ->whereRaw("{$haversine} < ?", [$radius]);
        }//search by radius

        $sort = !empty($request['sort']) ? $request['sort']:3;

        switch($sort)
        {
            case 1:
                $places->orderBy('name','asc');
                break;
            case 2:
                $places->orderBy('name','desc');
                break;
            case 3:
                $places->orderBy('distance','asc');
                break;
            case 4:
                $places->orderBy('distance','desc');
                break;
            default:
                $places->orderBy('distance','asc');
        }

        return $places->paginate(20);
    }

    /**
     * @param AddNewPlaceRequest $request
     * @return $this
     */
    public function saveNewPlace(AddNewPlaceRequest $request)
    {
        $place = new Place();
        $place->name= $request['name'];
        $place->image_url=!empty($request['image_url']) ? $request['image_url']:null;
        $place->summary=!empty($request['summary']) ? $request['summary']:null;
        $place->email_address=!empty($request['email_address']) ? $request['email_address']:null;
        $place->menu_link=!empty($request['menu_link']) ? $request['menu_link']:null;
        $place->website_url=!empty($request['website_url']) ? $request['website_url']:null;
        $place->facebook_link=!empty($request['facebook_link']) ? $request['facebook_link']:null;
        $place->instagram_link=!empty($request['instagram_link']) ? $request['instagram_link']:null;
        $place->google_place_id=!empty($request['google_place_id']) ? $request['google_place_id']:null;
        $place->has_vegan_options=!empty($request['has_vegan_options']) ;
        $place->has_gluten_free_options=!empty($request['has_gluten_free_options']);
        $place->is_food_truck=!empty($request['is_food_truck']) ;
        $place->serves_full_meals=!empty($request['serves_full_meals']) ? $request['serves_full_meals']:null;
        $place->serves_alcohol=!empty($request['serves_alcohol']) ? $request['serves_alcohol']:null;
        $place->has_public_wifi=!empty($request['has_public_wifi']) ? $request['has_public_wifi']:null;
        $place->has_bike_rack=!empty($request['has_bike_rack']) ? $request['has_bike_rack']:null;
        $place->has_carryout=!empty($request['has_carryout']) ? $request['has_carryout']:null;
        $place->save();
        $place->refresh();
        $this->buildPlaceInformation($place);
        return redirect()->route('editPlace',$place->id)->with('success_message', 'Success! You just added this place to EatLocalICT');

    }


    /**
     * Processes a claim from the currently logged-in user that they own the provided place
     *
     * @param PlaceOwnershipRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function claimOwnership(PlaceOwnershipRequest $request)
    {
        $claim = new UserPlaceOwnershipClaim();
        $claim->requester_user_id = Auth::id();
        $claim->place_id = $request['place'];
        $claim->save();

        return response(null,204);

    }

    /**
     * returns all locations owned by the current user
     * @return array the locations owned by the current user
     */
    public function showLocationsOwnedByUser()
    {
        if(Auth::check())
        {
            $ids = UserPlaceOwnershipClaim::select('place_id')
                ->distinct()
                ->where('requester_user_id',Auth::id())
                ->get()
                ->pluck('place_id')
                ->toArray();

            return Place::whereIn('id',$ids)
                ->select('id','name')
                ->get();
        }
        else
        {
            return [];
        }
    }//function showLocationsOwnedByUser


    /**
     * Allows a user to edit a listing that they own
     *
     * @param UserEditPlaceRequest $request HTTP request from frontend
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function editListing(UserEditPlaceRequest $request)
    {
        $place = Place::find($request['id']);
        $place->name=!empty($request['name']) ? $request['name']:null;
        $place->image_url=!empty($request['image_url']) ? $request['image_url']:null;
        $place->summary=!empty($request['summary']) ? $request['summary']:null;
        $place->email_address=!empty($request['email_address']) ? $request['email_address']:null;
        $place->menu_link=!empty($request['menu_link']) ? $request['menu_link']:null;
        $place->website_url=!empty($request['website_url']) ? $request['website_url']:null;
        $place->facebook_link=!empty($request['facebook_link']) ? $request['facebook_link']:null;
        $place->instagram_link=!empty($request['instagram_link']) ? $request['instagram_link']:null;
        $place->has_vegan_options=!empty($request['has_vegan_options']) ;
        $place->has_gluten_free_options=!empty($request['has_gluten_free_options']);
        $place->is_food_truck=!empty($request['is_food_truck']) ;
        $place->serves_full_meals=!empty($request['serves_full_meals']) ? $request['serves_full_meals']:null;
        $place->serves_alcohol=!empty($request['serves_alcohol']) ? $request['serves_alcohol']:null;
        $place->has_public_wifi=!empty($request['has_public_wifi']) ? $request['has_public_wifi']:null;
        $place->has_bike_rack=!empty($request['has_bike_rack']) ? $request['has_bike_rack']:null;
        $place->has_carryout=!empty($request['has_carryout']) ? $request['has_carryout']:null;
        $place->serves_brunch=!empty($request['serves_brunch']) ? $request['serves_brunch']:null;
        $place->has_delivery=!empty($request['has_delivery']) ? $request['has_delivery']:null;
        $place->has_ev_charger=!empty($request['has_ev_charger']) ? $request['has_ev_charger']:null;
        $place->wifi_password=!empty($request['wifi_password']) ? $request['wifi_password']:null;

        $this->updateTags($place,$request);

        $place->save();

        $place->fresh();

        $log = new PlaceUpdateLog();
        $log->place_id = $place->id;
        $log->edited_by_user_id = Auth::id();
        $log->request_json = $request->getContent();
        $log->new_model_json = $place->toJson(JSON_PRETTY_PRINT);
        $log->save();


        return response(null,204);
    }//function editListing

    /**
     * Updates the tags for the place
     *
     * TODO: This function is very similar to the one in the AdminController. Consider creating a trait to share functions between these two controllers
     *
     * @param Place $place
     * @param UserEditPlaceRequest $request
     */
    public function updateTags(Place $place, UserEditPlaceRequest $request)
    {
        $existing = PlaceTag::select('tag_id')
            ->where('place_id',$place->id)
            ->get()
            ->pluck('tag_id')
            ->toArray();
        $incoming = [];
        if(!empty($request['tags']))
        {
            foreach ($request['tags'] as $tag)
            {
                if(!empty($tag['id']))
                {
                    $incoming[]=$tag['id'];
                    //id exists, so this tag is in our database
                    if(!in_array($tag['id'],$existing))
                    {//it's not in the existing, so we must add it
                        PlaceTag::create([
                            'place_id'=>$place->id,
                            'tag_id'=>$tag['id']
                        ]);
                    }

                }
            }
        }

        //now delete all that don't need to be there anymore

        PlaceTag::where('place_id',$place->id)
            ->whereNotIn('tag_id',$incoming)
            ->delete();

    }//update tags





}//class
