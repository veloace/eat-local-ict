<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$backendDomain =config('app.backend_url');

Route::domain($backendDomain)
    ->middleware(['auth:web','administrators'])
    ->group(function () {
        //BEGIN ADMIN GROUP
    Route::get('','AdminController@index');
    Route::post('','AdminController@acceptSuggestion');
    Route::delete('','AdminController@deleteSuggestion');
    Auth::routes(['register'=>'false']);

    Route::prefix('place')->group(function(){
        Route::get('','AdminController@indexPlaces')->name('indexPlaces');
        Route::get('edit/{place}','AdminController@editPlace')->name('editPlace');
        Route::post('edit/{id}','AdminController@savePlaceEdits')->name('savePlaceEdits');
        Route::get('add','AdminController@addPlace')->name('addPlace');
        Route::post('add','AdminController@saveNewPlace')->name('saveNewPlace');


    });//place prefix



});//end  ADMIN GROUP


Route::get('/{vue_capture?}',function(){

    $data['token']=json_encode(['csrfToken' => csrf_token()]);
    return view('webAppShell',$data);
})->where('vue_capture', '[\/\w\.-]*')
    ->name('app');
