<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPlaceOwnershipClaim extends Model
{
    //
    use SoftDeletes;


    public function user()
    {
        return $this->hasOne(User::class,'id','requester_user_id');
    }

    public function place()
    {
        return $this->hasOne(Place::class,'id','place_id');
    }
}
