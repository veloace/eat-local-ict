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
    ->group(function () {


        Route::middleware(['auth:web','administrators'])->group(function () {
            //BEGIN REGULAR USERS GROUP
            Route::get('/home',function (){
                return redirect('/');
            });
            Route::get('','AdminController@index');

            //END REGULAR USERS GROUP
        });

        Route::middleware(['auth:web','administrators'])->group(function () {
            //BEGIN ADMIN GROUP

            Route::post('','AdminController@acceptSuggestion');
            Route::delete('','AdminController@deleteSuggestion');
            Route::prefix('place')->group(function(){
                Route::get('','AdminController@indexPlaces')->name('indexPlaces');
                Route::delete('','AdminController@deletePlace')->name('deletePlace');
                Route::get('edit/{place}','AdminController@editPlace')->name('editPlace');
                Route::post('edit/{id}','AdminController@savePlaceEdits')->name('savePlaceEdits');
                Route::get('add','AdminController@addPlace')->name('addPlace');
                Route::post('add','PlaceController@saveNewPlace')->name('saveNewPlace');


            });//place prefix

        });//ADMIN GROUP

        Auth::routes(['register'=>'false']);


});//backend


/*--AUTH ROUTES OVERRIDE---
*Using md5() hashes of routes to prevent vue collisions
*/
// Authentication Routes...must be outside of subdomain routing to work properly for app and for CSA.
Route::post('spaLogin','UserController@loginViaSpa')->name('user.spaLogin')->middleware('AddTokenToLogin');
Route::get('spaLogin',function(){
    $id = \Illuminate\Support\Facades\Auth::id();
    $user = \App\User::where('id',$id)->select('name')->first();
    return($user);
})->middleware('auth:web');

$this->get('d56b699830e77ba53855679cb1d252da', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('d56b699830e77ba53855679cb1d252da', 'Auth\LoginController@login');
$this->post('4236a440a662cc8253d7536e5aa17942', 'Auth\LoginController@logout')->name('logout');

//$this->get('29a41264ad2fc71b90534753a781e766', 'Auth\ForgotPasswordController@showLinkRequestForm');
$this->post('d7dc0e8839444523808953373d057581', 'Auth\ForgotPasswordController@sendResetLinkEmail');
$this->post('ad7dc0e8839444523808953373d057581', 'Auth\ResetPasswordController@reset');



Route::get('/{vue_capture?}',function(){

    $data['token']=json_encode(['csrfToken' => csrf_token()]);
    return view('webAppShell',$data);
})->where('vue_capture', '[\/\w\.-]*')
    ->name('app');
