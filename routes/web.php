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

Route::get('/', ['as'=>'home','uses'=>'ReportController@index'])->middleware('auth');
Auth::routes();
//Report route and actions which is allowed to admin only
Route::group(['prefix'=>'report', 'middleware' => 'auth'], function(){
    Route::get('/', ['as'=>'report.index', 'uses'=>'ReportController@index']);
    Route::get('/user/{userId}', ['as'=>'report.user.index', 'uses'=>'ReportController@index']);
    Route::get('/data/{userId?}', ['as'=>'report.list', 'uses'=>'ReportController@listData']);
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
//Defect routes
Route::group(['prefix' => 'defect','middleware'=>'auth'], function () {
	Route::get('{userId}/data', ['as' => 'defect.list', 'uses' => 'DefectController@listData'])->middleware('role:admin|employee');
	Route::get('/{userId}', ['as' => 'defect.index', 'uses' => 'DefectController@index'])->middleware('role:admin|employee');
	Route::get('{userId}/create', ['as' => 'defect.create', 'uses' => 'DefectController@create'])->middleware('role:admin');
	Route::post('{userId}/create', ['as' => 'defect.store', 'uses' => 'DefectController@store'])->middleware('role:admin');
	Route::get('{userId}/{id}/edit', ['as' => 'defect.edit', 'uses' => 'DefectController@edit'])->middleware('role:admin');
	Route::put('{userId}/{id}', ['as' => 'defect.update', 'uses' => 'DefectController@update'])->middleware('role:admin');
	Route::delete('{id}', ['as' => 'defect.destroy', 'uses' => 'DefectController@destroy'])->middleware('role:admin');
});
//Bonus routes
Route::group(['prefix' => 'bonus','middleware'=>'auth'], function () {
    Route::get('{userId}/data', ['as' => 'bonus.list', 'uses' => 'BonusController@listData'])->middleware('role:admin|employee');
    Route::get('/{userId}', ['as' => 'bonus.index', 'uses' => 'BonusController@index'])->middleware('role:admin|employee');
    Route::get('{userId}/create', ['as' => 'bonus.create', 'uses' => 'BonusController@create'])->middleware('role:admin');
    Route::post('{userId}/create', ['as' => 'bonus.store', 'uses' => 'BonusController@store'])->middleware('role:admin');
    Route::get('{userId}/{id}/edit', ['as' => 'bonus.edit', 'uses' => 'BonusController@edit'])->middleware('role:admin');
    Route::put('{userId}/{id}', ['as' => 'bonus.update', 'uses' => 'BonusController@update'])->middleware('role:admin');
    Route::delete('{id}', ['as' => 'bonus.destroy', 'uses' => 'BonusController@destroy'])->middleware('role:admin');
});

//Users routes
Route::group(['middleware'=>['auth','role:admin']],function ()
{
    Route::get('user/data', ['as'=>'user.list', 'uses'=>'UserController@listData']);
    Route::resource('user','UserController');    # code...
});

//Monthly Statistics routes
Route::group(['prefix' => 'statistics','middleware'=>['auth','role:employee']],function ()
{
   Route::get('/',['as'=>'statistics.view','uses'=>'StatisticsController@index']); 
   Route::post('/get',['as'=>'statistics.get','uses'=>'StatisticsController@get']); 
});