<?php

namespace App\Http\Controllers;

use App\GooglePlaceCache;
use App\Http\Requests\GetRandomPlaceRequest;
use App\Http\Requests\PlaceDescriptionSuggestionRequest;
use App\Place;
use App\PlaceDescriptionSuggestion;
use App\PlaceTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PlaceController extends Controller
{
//


    /**
     * @param GetRandomPlaceRequest $request
     * @param int $iterator
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getRandomPlace(GetRandomPlaceRequest $request,$iterator = 0)
    {

        $requires_open = empty($request['is_open']) ? false :(boolean)$request['is_open'];
        if(!empty($request['lat']) && !empty($request['lng']) )
        {
            //browser gps coordinates were supplied, so add them to the session.
            session([
                'lat'=>$request['lat'],
                'lng'=>$request['lng']
            ]);

        }

        $place = Place::select('id','name','address','city','state_code','summary','google_place_id','latitude','longitude')
            ->inRandomOrder()
            ->first()
            ->append(['is_open','user_distance']);
        $passesPreflight=true;
        //passes preflight indicates if the place is NOT a rerun and it matches the filters

            if($this->randomIsRerun($place->id))
            {
                $passesPreflight=false;
            }//if this is a rerun

            if($requires_open && ($place->is_open==false))
            {
                $passesPreflight=false;
            }//the place is required to be open but it is not open

            if(!$passesPreflight && $iterator<10)
            {
                $iterator++;
                return $this->getRandomPlace($request,$iterator);
            }//if preflight failed and interator is less than 10
            else
            {
                return $place;
            }//else

    }//function getRandomPlaces


    /**
     * determines if this place is a rerun (has been issued to the user in this session)
     * @param $placeID
     * @return bool
     */
    private function randomIsRerun($placeID)
    {
        $runs = session('runs',[]);//retrieve the runs array from the session, or else initialize the empty array.

        //if the rerun array exceeds 50, clear out the first element
        if(count($runs)>50)
        {
            array_shift($runs);//removes the first item
        }
        if(in_array($placeID,$runs))
        {//it's a rerun
            return true;
        }
        else
        {
            //it's not a rerun, so save the id to the session so that we ignore it in the future
            $runs[] = $placeID;
            session(['runs'=>$runs]);
            return false;
        }

    }

    /**
     * @param Place $place
     * @param Request $request
     * @return array
     */
    public function index(Place $place)
    {

        $place->append('user_distance');
        if(!empty($_GET['lat']) && !empty($_GET['lng']) )
        {
            //browser gps coordinates were supplied, so add them to the session.
            session([
                'lat'=>$_GET['lat'],
                'lng'=>$_GET['lng']
            ]);

        }

        if (empty($place->google_place_id))
        {
            $place =$this->buildPlaceInformation($place);

        }
        $return = $place->toArray();
        $return['map_link']=$place->map_link;
        $return['tags']=$place->tags;


        $cached =GooglePlaceCache::where([
            ['google_place_id',$place->google_place_id],
            ['updated_at', '>=', \Carbon\Carbon::now()->subWeek()]
        ])->first();


        if($cached)
        {
            $response = json_decode($cached->cached_content);
            $return['hours']=!empty($response->result->opening_hours->weekday_text) ? $response->result->opening_hours->weekday_text:[];
            $return['is_open']=$place->is_open;//since this information is cached, use our function to determine if they are open
            $return['price']=!empty($response->result->price_level) ? $response->result->price_level:1;
            $return['rating']=!empty($response->result->rating) ? $response->result->rating:5;
            $return['reviews']=!empty($response->result->reviews) ? $response->result->reviews:null;


        }
        else
        {
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

            }
        }


        return($return);
    }

    /**
     * @param null $tag
     * @return mixed
     */
    public function indexByTagID($tag=null)
    {
        if(!empty($tag)) {

            $ids = PlaceTag::where('tag_id', $tag)->select('place_id')->pluck('place_id')->toArray();
            return Place::whereIn('id', $ids)
                ->select('id', 'name', 'description', 'image_url')
                ->paginate(2);
        }
        else
        {
            return Place::paginate(20);
        }

    }

    /**
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
    }



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


    public function placeDescriptionSuggestion(PlaceDescriptionSuggestionRequest $request)
    {
        $suggestion = new PlaceDescriptionSuggestion();
        $suggestion->place_id = $request['id'];
        $suggestion->description = $request['description'];
        $suggestion->user_id = Auth::check() ? Auth::id() : null;
        $suggestion->save();
        return response(null,204);
    }


}//class
