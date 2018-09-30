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
        $query ="{$this->name} {$this->address} {$this->city}, {$this->state}";//TODO: add lat/long
        $query= urlencode($query);
        return "http://maps.apple.com/?q={$query}";
    }

    public function getTagsAttribute()
    {
        return PlaceTag::where('place_id',$this->id)->pluck('tag_id')->toArray();
    }
}


