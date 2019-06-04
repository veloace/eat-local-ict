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


    Route::prefix('user')->group(function(){
        route::get('','UserController@currentUser');
        route::patch('','UserController@updateUser');
        route::post('login','UserController@loginViaSpa');
        route::post('register','Auth\RegisterController@register');
        route::post('password','UserController@changePassword');
        route::post('deleteAccount','UserController@deleteAccount');

    });







Route::prefix('places')->group(function () {
    Route::get('index/{place}', 'PlaceController@index');
    Route::get('/owner', 'PlaceController@showLocationsOwnedByUser');
    Route::get('/owner/{place}', 'PlaceController@indexLocationOwnedByUser');
    Route::post('/owner/edit', 'PlaceController@editListing');
    Route::post('/owner', 'PlaceController@claimOwnership');
    Route::get('search', 'PlaceController@search');
    Route::get('random', 'PlaceController@getRandomPlace');
    Route::post('description', 'PlaceController@placeDescriptionSuggestion');
    Route::post('missing', 'PlaceController@missingPlaceSuggestion');
    Route::prefix('lists')->group(function () {
        Route::post('favorites', 'UserListConroller@addToFavorites');
        Route::post('favorites/delete', 'UserListConroller@deleteFavorites');
        Route::post('savedForLater', 'UserListConroller@addToSavedForLater');
        Route::post('savedForLater/delete', 'UserListConroller@deleteSavedForLater');
    });


});

