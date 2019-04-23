<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSavedForLater extends Model
{
    //
    use SoftDeletes;
    protected $fillable=['place_id','user_id'];

}
