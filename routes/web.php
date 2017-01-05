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
    Route::get('create/step1', ['as'=>'report.create.step1', 'uses'=>'ReportController@create']);
    Route::get('create/step2', ['as'=>'report.create.step2', 'uses'=>'ReportController@createStepTwo']);
    Route::get('/', ['as'=>'report.index', 'uses'=>'ReportController@index']);
});

