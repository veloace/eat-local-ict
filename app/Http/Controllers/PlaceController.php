<?php

namespace App\Http\Controllers;

use App\GooglePlaceCache;
use App\Place;
use App\PlaceHour;
use App\PlaceTag;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
//


    /**
     * @param int $iterator
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getRandomPlace($iterator = 0)
    {

        $top = Place::whereRaw('id = (select max(`id`) from places)')->get()->pluck('id')->first();
        $id = rand(1,$top);

        $place = Place::where('id',$id)
            ->select('id','name','address','city','state_code','summary')
            ->first();


        if($place)
        {
            //if model exits, return it, but first make sure we prevent reruns (showing the same location too many times in one session)(

            if($this->randomIsRerun($id))
            {
                //it's a rerun, so try to get one that is not a rerun
                if($iterator <3)
                {//only try this three times
                    return $this->getRandomPlace($iterator+1);

                }//if
                else
                {
                    //we tried three times, just send it
                    return $place;
                }//else
            }//if is rerun
            else
            {
                return $place;

            }//else
        }
        elseif($iterator <3)
        {//if model does not exist, redo the process (but only try 3 times)
            return $this->getRandomPlace($iterator+1);
        }
        else
        {
            return response('',420);
        }
    }


    private function randomIsRerun($placeID)
    {
        $runs = session('runs',[]);//retrieve the runs array from the session, or else initialize the empty array.
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
     * @return Place
     */
    public function index(Place $place)
    {


        if (empty($place->google_place_id))
        {
            $place =$this->buildPlaceInformation($place);

        }
        $return = $place->toArray();
        $return['map_link']=$place->map_link;
        $return['tags']=$place->tags;


        $cached =GooglePlaceCache::where([
            ['google_place_id',$place->google_place_id],
            ['updated_at', '>=', \Carbon\Carbon::now()->subDay()]
        ])->first();


        if($cached)
        {
            $response = json_decode($cached->cached_content);
            $return['hours']=!empty($response->result->opening_hours->weekday_text) ? $response->result->opening_hours->weekday_text:[];
            $return['is_open']=$this->isPlaceOpen($response);//since this information is cached, use our function to determine if they are open
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

    /**
     * @param $response
     * @return boolean
     */
  public function isPlaceOpen($response)
  {
      $now =  new Carbon();
      $now->tz='America/Chicago';

      try {
          foreach ($response->result->opening_hours->periods as $period) {
             // echo Carbon::create($year, $month, $day, $hour, $minute, $second, $tz)."\n";

              if($period->open->day==$period->close->day)
              {//opens and closes on the same day
                  if($now->dayOfWeek == $period->open->day)
                  {//if that day is today, then it is time to use the time to determine if it is open
                      $openTime = (integer)$period->open->time;
                      $closeTime =(integer)$period->close->time ==0 ? 2400:(integer)$period->close->time ;
                      $now = (integer)$now->format('Hi');
                      return(($openTime<=$now) && ($closeTime >$now));
                  }
              }
              elseif($period->open->day < $period->close->day)
              {//normal situation

                  //if today is the same or greater than the open day AND today is less than or same as the close day
                  if(($now->dayOfWeek>=$period->open->day) && ($now->dayOfWeek<=$period->close->day))
                  {//
                     if($now->dayOfWeek<$period->close->day)
                     {//if today is before the close day, return true as the restaurant is open all day.
                         return true;
                     }
                     else //else close day is equal to today
                     {//make sure today's time is before the close time
                         $closeTime =(integer)$period->close->time ==0 ? 2400:(integer)$period->close->time ;
                         $now = (integer)$now->format('Hi');
                         return(($closeTime >$now));
                     }
                  }
                  //(else) do nothing
              }//if open day < closing day
              else
              {//opening day is greater than the closing day, a weird situation
                  if($now->dayOfWeek > $period->close->day && $now->dayOfWeek < $period->open->day)
                  {
                      return false;
                  }
                  else
                  {
                      if($now->dayOfWeek<$period->close->day)
                      {//if today is before the close day, return true as the restaurant is open all day.
                          return true;
                      }
                      else //else close day is equal to today
                      {//make sure today's time is before the close time
                          $closeTime =(integer)$period->close->time ==0 ? 2400:(integer)$period->close->time ;
                          $now = (integer)$now->format('Hi');
                          return(($closeTime >$now));
                      }
                  }
              }//else weirdness
          }//foreach

          return false;

          /*
           *  Days
           * 0 - Sunday
           * 1 - Monday
           * 2 - Tuesday
           * 3 - Wednesday
           * 4 - Thursday
           * 5 - Friday
           * 6 - Saturday
           */

      }
      catch (\Exception $e)
      {
          //
          return true;//fail-safe
      }

  }//isPlaceOpen function


}//class
