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

Route::get('/', ['as'=>'home',function () {
	return view('welcome');
}]);

Auth::routes();



//Report route and actions which is allowed to admin only
Route::group(['prefix'=>'report', 'middleware' => 'auth'], function(){
    Route::get('/', ['as'=>'report.index', 'uses'=>'ReportController@index']);
    Route::get('/data', ['as'=>'report.list', 'uses'=>'ReportController@listData']);
    Route::get('create/', ['as'=>'report.create.step1', 'uses'=>'ReportController@create'])->middleware('role:admin');
    Route::get('create/{id}', ['as'=>'report.create.step2', 'uses'=>'ReportController@createStepTwo'])->middleware('role:admin');
    Route::post('create', ['as'=>'report.store', 'uses'=>'ReportController@store'])->middleware('role:admin');
    Route::get('{id}', ['as'=>'report.show', 'uses'=>'ReportController@show']);
    Route::get('participate/{id}', ['as'=>'report.getParticipate', 'uses'=>'ReportController@getParticipate']);
    Route::put('participate/{id}', ['as'=>'report.putParticipate', 'uses'=>'ReportController@putParticipate']);
    Route::get('{id}/edit', ['as'=>'report.edit', 'uses'=>'ReportController@edit'])->middleware('role:admin');
    Route::put('{id}', ['as'=>'report.update', 'uses'=>'ReportController@update'])->middleware('role:admin');
    Route::delete('{id}', ['as'=>'report.destroy', 'uses'=>'ReportController@destroy'])->middleware('role:admin');
});

Route::group(['prefix' => 'defect','middleware'=>'role:admin'], function () {
	Route::get('{userId}/data', ['as' => 'defect.list', 'uses' => 'DefectController@listData']);
	Route::get('/{userId}', ['as' => 'defect.index', 'uses' => 'DefectController@index']);
	Route::get('{userId}/create', ['as' => 'defect.create', 'uses' => 'DefectController@create']);
	Route::post('{userId}/create', ['as' => 'defect.store', 'uses' => 'DefectController@store']);
	Route::get('{id}/edit', ['as' => 'defect.edit', 'uses' => 'DefectController@edit']);
	Route::put('{userId}/{id}', ['as' => 'defect.update', 'uses' => 'DefectController@update']);
	Route::delete('{id}', ['as' => 'defect.destroy', 'uses' => 'DefectController@destroy']);
});
