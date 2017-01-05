<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['prefix'=>'report'], function(){
    Route::get('/data', ['as'=>'report.list', 'uses'=>'ReportController@listData']);
    Route::get('/', ['as'=>'report.index', 'uses'=>'ReportController@index']);
    Route::get('create/', ['as'=>'report.create.step1', 'uses'=>'ReportController@create']);
    Route::get('create/{id}', ['as'=>'report.create.step2', 'uses'=>'ReportController@createStepTwo']);
    Route::post('create', ['as'=>'report.store', 'uses'=>'ReportController@store']);
    Route::get('{id}', ['as'=>'report.show', 'uses'=>'ReportController@show']);
    Route::get('{id}/edit', ['as'=>'report.edit', 'uses'=>'ReportController@edit']);
    Route::put('{id}', ['as'=>'report.update', 'uses'=>'ReportController@update']);
    Route::delete('{id}', ['as'=>'report.destroy', 'uses'=>'ReportController@destroy']);
});

