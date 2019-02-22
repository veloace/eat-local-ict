<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    //
    //protected $appends=['hours','map_link','tags'];



    public function getMapLinkAttribute()
    {
        $query ="{$this->name}";
        $address = "{$this->name} {$this->address} {$this->city}, {$this->state}";
        $query= urlencode($query);
        return "http://maps.apple.com/?q={$query}&address={$address}";
    }

    public function getTagsAttribute()
    {
        return PlaceTag::where('place_id',$this->id)->pluck('tag_id')->toArray();
    }

    public function getIsOpenAttribute()
    {

        $response = $this->getCachedAPI();
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

    }//is_open


    public function getCachedAPI()
    {
        $cached =GooglePlaceCache::where([
            ['google_place_id',$this->google_place_id],
            ['updated_at', '>=', Carbon::now()->subWeek()]
        ])->first();

        return $cached ? json_decode($cached->cached_content): $this->refreshCache();
    }


    public function refreshCache()
    {

    if($this->google_place_id)
    {
        $key = config('app.PLACES_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$this->google_place_id}&fields=opening_hours,rating,price_level,reviews&key={$key}";
        if($response=@json_decode(@file_get_contents($url))) {
            GooglePlaceCache::updateOrCreate(
                ['google_place_id'=>$this->google_place_id],
                ['cached_content'=>json_encode($response)]
            );
            return $response;
        }
    }


        return null;
    }
}


