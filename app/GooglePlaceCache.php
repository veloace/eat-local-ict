<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GooglePlaceCache extends Model
{
    //
    protected $fillable=['google_place_id','cached_content'];
}
