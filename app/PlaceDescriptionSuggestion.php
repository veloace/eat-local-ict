<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaceDescriptionSuggestion extends Model
{
    //

    public function place()
    {
        return $this->hasOne(Place::class,'id','place_id');
    }
}
