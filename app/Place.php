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
        if(!empty($this->google_place_id))
        {
            return "https://www.google.com/maps/place/?q=place_id:{$this->google_place_id}";
        }
        $query ="{$this->name} {$this->address} {$this->city}, {$this->state}";
        $query= urlencode($query);
        return "http://maps.apple.com/?q={$query}";
    }

    public function getTagsAttribute()
    {
        return PlaceTag::where('place_id',$this->id)->pluck('tag_id')->toArray();
    }
}


