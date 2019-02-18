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

Route::prefix('backend')
    ->middleware(['auth:web','administrators'])
    ->group(function () {
    Route::get('','AdminController@index');

});

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
