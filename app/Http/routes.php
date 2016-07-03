<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    if(\Illuminate\Support\Facades\Auth::user()) return redirect('/tasks');

    return view('welcome');
});

Route::auth();

Route::get('/home', function (){
    return redirect('/tasks');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/tasks', 'TaskController@index');
    Route::get('/new_task', 'TaskController@create');
    Route::post('/new_task', 'TaskController@store');
    Route::get('/task/{task}', ['as' => 'task', 'uses' => 'TaskController@get']);

    Route::get('/multilogin/new', 'MultiLoginController@create');
    Route::post('/multilogin/new', 'MultiLoginController@store');

    Route::get('/multilogin', 'MultiLoginController@index');
    Route::get('/multilogin/settings', 'MultiLoginController@settings');
    Route::post('/multilogin/settings', 'MultiLoginController@setSettings');
    Route::get('/multilogin/{task}/items', ['as' => 'items', 'uses' => 'MultiLoginController@items']);

    Route::get('/multilogin/{item}/delete', ['as' => 'delete', 'uses' => 'MultiLoginController@delete']);
    Route::get('/multilogin/task/{task}/delete', ['as' => 'delete_task', 'uses' => 'MultiLoginController@deleteTask']);

    Route::get('/multilogin/deleteByRange', 'MultiLoginController@deleteByRange');
    Route::post('/multilogin/deleteByRange', 'MultiLoginController@deleteByRangeDo');

    Route::get('/domains', 'TaskController@domains');
    Route::get('/domains/download', 'TaskController@download');

    Route::get('/delete', 'TaskController@delete');
    Route::post('/delete', 'TaskController@deleteDo');
    Route::post('/delete/complete', 'TaskController@deleteComplete');

    Route::get('/delete/tasks', 'TaskController@deleteTasks');
});