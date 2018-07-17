<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    //
    protected $appends=['hours','map_link','tags'];

    public function getHoursAttribute()
    {
        $hours = PlaceHour::where('place_id',$this->id)
            ->orderBy('day_of_week', 'asc')
            ->get();
        $return = [];

        foreach ($hours as $hour)
        {
            $day = jddayofweek($hour->day_of_week,1);

            $return[$day] = [
                'open'=>(new Carbon($hour->open_time))->format('g:i A'),
                'close'=>(new Carbon($hour->close_time))->format('g:i A')
            ];
        }

        return ($return);
    }


    public function getMapLinkAttribute()
    {
        $query ="{$this->name} {$this->address} {$this->city}, {$this->state}";//TODO: add lat/long
        $query= urlencode($query);
        return "http://maps.apple.com/?q={$query}";
    }

    public function getTagsAttribute()
    {
        return PlaceTag::where('place_id',$this->id)->pluck('tag_id')->toArray();
    }
}


