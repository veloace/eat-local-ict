<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToListRequest;
use App\UserFavorite;
use App\UserSavedForLater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserListConroller extends Controller
{
    //
    function addToFavorites(AddToListRequest $request)
    {
        $list = UserFavorite::firstOrCreate(
            ['user_id' => Auth::id(),
            'place_id' => $request->place ]
        );
        return response()->json(['id'=>$list->id]);
    }

    function addToSavedForLater(AddToListRequest $request)
    {
        $list = UserSavedForLater::firstOrCreate(
            ['user_id' => Auth::id(),
                'place_id' => $request->place ]
        );
        return response()->json(['id'=>$list->id]);

    }

    function showFavorites()
    {

    }

    function showSavedForLater()
    {

    }
}
