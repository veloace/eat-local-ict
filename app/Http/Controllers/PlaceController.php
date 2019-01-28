<?php

namespace App\Http\Controllers;

use App\GooglePlaceCache;
use App\Place;
use App\PlaceTag;
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
    public function index($place)
    {
        $place =Place::where('id',$place)->first();
        $return = $place->toArray();
        $return['map_link']=$place->map_link;
        $return['tags']=$place->tags;


        $cached = GooglePlaceCache::where([
            ['google_place_id',$place->google_place_id],
            ['updated_at', '>=', \Carbon\Carbon::now()->subHour()]
        ])->first();


        if($cached)
        {
            $response = json_decode($cached->cached_content);
            $return['hours']=!empty($response->result->opening_hours->weekday_text) ? $response->result->opening_hours->weekday_text:[];
            $return['is_open']=!empty($response->result->opening_hours->open_now) ? $response->result->opening_hours->open_now:false;
            $return['price']=!empty($response->result->price_level) ? $response->result->price_level:1;
            $return['rating']=!empty($response->result->rating) ? $response->result->rating:5;
        }
        else
        {

            $key = config('app.PLACES_API_KEY');
            $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place->google_place_id}&fields=opening_hours,rating,price_level&key={$key}";
            if($response=@json_decode(@file_get_contents($url))) {
                $return['hours']=!empty($response->result->opening_hours->weekday_text) ? $response->result->opening_hours->weekday_text:[];
                $return['is_open']=!empty($response->result->opening_hours->open_now) ? $response->result->opening_hours->open_now:false;
                $return['price']=!empty($response->result->price_level) ? $response->result->price_level:1;
                $return['rating']=!empty($response->result->rating) ? $response->result->rating :5;

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
            return Place::paginate(2);
        }

    }
}
