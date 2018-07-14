<?php

namespace App\Http\Controllers;

use App\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
//


    public function getRandomPlace($iterator = 0)
    {

        $top = Place::whereRaw('id = (select max(`id`) from places)')->get()->pluck('id')->first();
        $id = rand(1,$top);

        $place = Place::where('id',$id)
            ->select('id','name','address','city','state_code')
            ->first();


        if($place)
        {
            //if model exits, return it
            return $place;
        }
        elseif($iterator <3)
        {//if model does not exist, redo the process (but only try 3 times)
            return $this->getRandomPlace($iterator+1);
        }
        else
        {
            return response('',420);
        }
    }


    public function index(Place $place)
    {
        return $place;
    }
}
