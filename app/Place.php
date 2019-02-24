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

    /**
     * determines (using JSON from the Google Places API) if the location is open or closed at this exact time.
     * @return bool
     */
    public function getIsOpenAttribute()
    {

        $response = $this->getCachedAPI();
        $now =  new Carbon();
        $now->tz='America/Chicago';
        $today=$now->dayOfWeek;
        $currentTime=$now->format('Hi');
        //

        if(!empty($response->result->opening_hours))
        {
            $periods =$response->result->opening_hours->periods;        //this usort should be relevant as the opening hours should already be sorted correctly--but let's explicitly program that
            usort($periods,function($a,$b){
                return $a->open->day > $b->open->day;
            });

            foreach ($response->result->opening_hours->periods as $period) {
                $openDay =(integer) $period->open->day;
                $openTime =(integer)  $period->open->time;
                $closeDay =(integer) $period->close->day;
                $closeTime =(integer)  $period->close->time ==0 ? 2400:(integer)$period->close->time;

                if(($openDay == $today) &&($closeDay== $today))
                {//easy one, today is equal to the open day and close day, so we only need to look at time
                    if(($openTime<=$currentTime)&&($closeTime>= $currentTime))
                    {
                        //current time is between the open time and the close time so it is open
                        return true;
                    }
                }//if $close==$open==$now
                elseif(($openDay == $today) &&($closeDay!=$today))
                {
                    //close day is not today, but today is the open day, so we just need to make sure we are passed the open time
                    if(($openTime<=$currentTime))
                    {
                        //current time is greater than the open time, so it's open
                        return true;
                    }
                }//elseif $open==$now, but $close != now
                elseif(($closeDay == $today) &&($openDay!=$today))
                {//open day is not today, but the close day is today
                    if(($currentTime<$closeTime))
                    {
                        //current time is less than the close time, so it's open
                        return true;
                    }
                }//elseif $open!=today, but $close ==$today
                elseif(($closeDay > $today) &&($openDay<$today))
                {//
                    //today falls between the open days, meaning it neither opens nor closes today
                    return true;
                }//if open < today < close
                elseif ( $closeDay < $openDay)
                {//weird scenario
                    /*this weird, because it indicates a span of open and close that spans a week, like Saturday to Monday, where the open day
                    is 6 and the close day is 1, so we can't use standard math as that would give us a false close
                    */
                    if(($today<$closeDay) || ($openDay < $today ))
                    {
                        return true;
                    }
                }


            }//foreach
        }
        return false;//default value

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


