<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaceTag extends Model
{
    //

    public function places()
    {
        return $this->hasMany(Place::class,'id','place_id')
            ->select(['id','name','description','image_url']);
    }
}
