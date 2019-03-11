<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    //
    protected $appends=['user_distance','is_open'];


    /**
     * Generates the Apple Maps navigation link for use by the frontend
     * @return string
     */
    public function getMapLinkAttribute()
    {
        $query ="{$this->name}";
        $address = "{$this->name} {$this->address} {$this->city}, {$this->state}";
        $query= urlencode($query);
        return "http://maps.apple.com/?q={$query}&address={$address}";
    }


    /**
     * determines (using JSON from the Google Places API) if the location is open or closed at this exact time.
     * @return bool
     */
    public function getIsOpenAttribute()
    {

        try {
            $response = $this->getCachedAPI();
            $now = new Carbon();
           $now->tz = 'America/Chicago';
            $today = $now->dayOfWeek;
            $currentTime = $now->format('Hi');
            //

            if (!empty($response->result->opening_hours)) {
                $periods = $response->result->opening_hours->periods;        //this usort should be relevant as the opening hours should already be sorted correctly--but let's explicitly program that
                usort($periods, function ($a, $b) {
                    return $a->open->day > $b->open->day;
                });

                foreach ($response->result->opening_hours->periods as $period) {
                    $openDay = (integer)$period->open->day;
                    $openTime = (integer)$period->open->time;
                    $closeDay = (integer)$period->close->day;

                    if($period->close->time == 0)
                    {
                        $closeTime = 2400;
                        $closeDay = $closeDay== 0 ? 6:$closeDay-1;

                    }
                    else
                    {
                        $closeTime = (integer)$period->close->time;

                    }

                    if (($openDay == $today) && ($closeDay == $today)) {//easy one, today is equal to the open day and close day, so we only need to look at time
                        if (($openTime <= $currentTime) && ($closeTime >= $currentTime)) {
                            //current time is between the open time and the close time so it is open
                            return true;
                        }
                    }//if $close==$open==$now
                    elseif (($openDay == $today) && ($closeDay != $today)) {
                        //close day is not today, but today is the open day, so we just need to make sure we are passed the open time
                        if (($openTime <= $currentTime)) {
                            //current time is greater than the open time, so it's open
                            return true;
                        }
                    }//elseif $open==$now, but $close != now
                    elseif (($closeDay == $today) && ($openDay != $today)) {//open day is not today, but the close day is today
                        if (($currentTime < $closeTime)) {
                            //current time is less than the close time, so it's open
                            return true;
                        }
                    }//elseif $open!=today, but $close ==$today
                    elseif (($closeDay > $today) && ($openDay < $today)) {//
                        //today falls between the open days, meaning it neither opens nor closes today

                        return true;
                    }//if open < today < close
                    elseif ($closeDay < $openDay) {//weird scenario
                        /*this weird, because it indicates a span of open and close that spans a week, like Saturday to Monday, where the open day
                        is 6 and the close day is 1, so we can't use standard math as that would give us a false close
                        */
                        if (($today < $closeDay) || ($openDay < $today)) {
                            return true;
                        }
                    }


                }//foreach
            }
            return false;//default value
        }
        catch (\Exception $e)
        {
            return false;
        }//false

    }//is_open

    /**
     * gets the cached Google Places API information from the database if it is still fresh enough (less than one week old)
     * If it's over 1 week old, it will call a function to make the API call
     * @return mixed|null
     */
    public function getCachedAPI()
    {
        $cached =GooglePlaceCache::where([
            ['google_place_id',$this->google_place_id],
            ['updated_at', '>=', Carbon::now()->subWeek()]
        ])->first();
        return $cached ? json_decode($cached->cached_content): $this->refreshCache();
    }


    /**
     * updates our Cache of the Google Places API information by calling the Google Places API
     * @return mixed|null
     */
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


    /**
     * determines if the necessary data is there to calculate the distance between the user and this location.
     * @return float|null
     */
    public function getUserDistanceAttribute()
    {
        $lat = session('lat');
        $lng = session('lng');
        if(!empty($lat) && !empty($lng) && !empty($this->latitude) && !empty($this->longitude))
        {//if we have coordinates for this user and this place, we can calculate distance
            return $this->calculateDistance($lat, $lng, $this->latitude,$this->longitude);
        }

        return null;
    }

    /**
     * Calculates the spherical straight-line distance of between the the two sets of points
     * @param $lat1 float latitude of point 1
     * @param $lng1 float longitude of point 1
     * @param $lat2 float latitude of point 2
     * @param $lng2 float longitude of point 2
     * @return float
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $radiusEarth=3959;
        $deltaLat = deg2rad($lat2 -$lat1);
        $deltaLong =deg2rad($lng2-$lng1);
        $lat1 = deg2rad($lat1);
        $lat2=deg2rad($lat2);
        $a = sin($deltaLat/2)*sin($deltaLat/2)+sin($deltaLong/2)*sin($deltaLong/2)*cos($lat1)*cos($lat2);
        $c = 2*atan2(sqrt($a), sqrt(1-$a));
        return (round($radiusEarth*$c,2));
    }



}


