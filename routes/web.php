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

Route::get('/{vue_capture?}',function(){

    $tag = \App\Tag::all()
        ->pluck('name')
        ->toArray();
    $data['token']=json_encode(['csrfToken' => csrf_token()]);
    $data['tags'] = json_encode($tag);
    return view('webAppShell',$data);
})->where('vue_capture', '[\/\w\.-]*')
    ->name('app');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



