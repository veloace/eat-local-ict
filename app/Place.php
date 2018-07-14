<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    //
    protected $appends=['hours'];

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
}
