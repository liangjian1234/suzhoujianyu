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

Route::namespace('Home')->group(function(){
    Route::get('/','HomeController@index');
    Route::get('search','HomeController@search')->name('search');
    Route::get('myorder','HomeController@order')->name('myorder');
    Route::post('store','HomeController@store')->name('myorder.store');
    Route::get('myrecord','HomeController@record')->name('myrecord');
    Route::get('add/{id}','HomeController@add')->name('add');
    Route::get('delete/{id}','HomeController@delete')->name('delete');
});

Route::namespace('Admin')->prefix('admin')->group(function(){
    Route::get('/','AdminController@index')->name('admin');
    Route::post('import','AdminController@import')->name('import');
    Route::get('store','AdminController@store')->name('import.store');
    Route::any('export','AdminController@export')->name('export');
    Route::get('export/download','AdminController@download_export')->name('export.download');
    Route::get('record','AdminController@record')->name('admin.record');
});

