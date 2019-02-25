<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('session',function(){
    dd(session('runs'),session('lat'),session('lng'));
});
Route::prefix('places')->group(function () {
    Route::get('index/{place}', 'PlaceController@index');
    Route::get('tag/{tag?}', 'PlaceController@indexByTagID');
    Route::get('random', 'PlaceController@getRandomPlace');
    Route::post('description', 'PlaceController@placeDescriptionSuggestion');
});