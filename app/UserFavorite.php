<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFavorite extends Model
{
    //
    use SoftDeletes;
    protected $fillable=['place_id','user_id'];
}
