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

Auth::routes();
Route::get('/{vue_capture?}',function(){
    $tags = \App\Tag::select('name','id')
        ->get()
        ->toArray();
    $data['token']=json_encode(['csrfToken' => csrf_token()]);
    $data['tags'] = json_encode($tags);
    return view('webAppShell',$data);
})->where('vue_capture', '[\/\w\.-]*')
    ->name('app');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');




Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
