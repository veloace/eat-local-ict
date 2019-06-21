<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToListRequest;
use App\Http\Requests\DeleteFromListRequest;
use App\UserFavorite;
use App\UserSavedForLater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserListController extends Controller
{
    /**
     * Adds a place to a user's "favorites" list
     *
     * @param AddToListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    function addToFavorites(AddToListRequest $request)
    {
        $list = UserFavorite::where(
            ['user_id' => Auth::id(),
            'place_id' => $request->place ]
        )->first();

        if(!$list)
        {
            $list = new UserFavorite();
            $list->user_id = Auth::id();
            $list->place_id= $request->place;
        }
        $list->comment = !empty($request['comment']) ? $request['comment']:null;
        $list->save();
        return response()->json(['id'=>$list->id]);
    }//addToFavorites

    /**
     * Adds a place to a user's "save for later" list
     *
     * @param AddToListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    function addToSavedForLater(AddToListRequest $request)
    {
        $list = UserSavedForLater::firstOrCreate(
            ['user_id' => Auth::id(),
                'place_id' => $request->place ]
        );
        return response()->json(['id'=>$list->id]);

    }//addToSavedForLater

    /**
     * Deletes a place from a user's "favorites" list
     *
     * @param DeleteFromListRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function deleteFavorites(DeleteFromListRequest $request)
    {

        $list = UserFavorite::where([
            ['place_id',$request['id']],
            ['user_id',Auth::id()]//make sure that the provided ID actually belongs to the current user.
        ])->first();

        if($list)
        {
            $list->delete();
            return response(null,204);
        }//if it exists
        else
        {
            return response('This favorite does not appear to belong to you.',403);
        }//else
    }//deleteFavorites

    /**
     * Deletes a place from a user's "saved for later" list
     *
     * @param DeleteFromListRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function deleteSavedForLater(DeleteFromListRequest $request)
    {
        $list = UserSavedForLater::where([
            ['place_id',$request['id']],
            ['user_id',Auth::id()]//make sure that the provided ID actually belongs to the current user.
        ])->first();

        if($list)
        {
            $list->delete();
            return response(null,204);
        }//if it exists
        else
        {
            return response('This list item does not appear to belong to you.',403);
        }//else
    }//deleteSavedForLater
}
