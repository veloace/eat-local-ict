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

        Auth::routes(['register'=>'false']);

        Route::middleware(['auth:web','administrators'])->group(function () {
            //BEGIN ADMIN GROUP


            Route::prefix('webAPI')->group(function() {
                Route::get('', 'AdminController@loadDashboard');
                Route::get('/claims', 'AdminController@showOwnershipClaims');
                Route::post('/claims', 'AdminController@processOwnershipClaim');

                Route::post('', 'AdminController@acceptSuggestion');
                Route::delete('', 'AdminController@deleteSuggestion');


                Route::prefix('place')->group(function () {
                    Route::get('', 'AdminController@indexPlaces')->name('indexPlaces');
                    Route::delete('', 'AdminController@deletePlace')->name('deletePlace');
                    Route::get('edit/{place}', function($place){

                        $place = \App\Place::with('tags')->find($place);
                        $data['tags'] = \App\Tag::select('id','name')->get();
                         $data['listing'] = $place;
                         //
                         $data['previous'] = \App\Place::where('id','<',$place->id)
                             ->orderBy('id','desc')
                             ->first();
                         $data['previous'] = empty($data['previous']) ? null:$data['previous']->id;
                         //
                         $data['next'] = \App\Place::where('id','>',$place->id)
                             ->orderBy('id','asc')
                             ->first();
                        $data['next'] = empty($data['next']) ? null:$data['next']->id;

                            return $data;
                    })->name('editPlace');
                    Route::post('edit/', 'AdminController@savePlaceEdits')->name('savePlaceEdits');
                    Route::get('add', 'AdminController@addPlace')->name('addPlace');
                    Route::post('add', 'PlaceController@saveNewPlace')->name('saveNewPlace');


                });//place prefix
            });//webAPI prefix



            Route::get('/{vue_capture?}','AdminController@index')->where('vue_capture', '[\/\w\.-]*');

        });//ADMIN GROUP



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

Route::get('d56b699830e77ba53855679cb1d252da', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('d56b699830e77ba53855679cb1d252da', 'Auth\LoginController@login');
Route::post('4236a440a662cc8253d7536e5aa17942', 'Auth\LoginController@logout')->name('logout');

//$this->get('29a41264ad2fc71b90534753a781e766', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('d7dc0e8839444523808953373d057581', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('ad7dc0e8839444523808953373d057581', 'Auth\ResetPasswordController@reset');


Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');


Route::get('/{vue_capture?}',function(){

    $data['token']=json_encode(['csrfToken' => csrf_token()]);
    $data['tags'] = \App\Tag::select('id','name')->get();

    return view('webAppShell',$data);
})->where('vue_capture', '[\/\w\.-]*')
    ->name('app');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
