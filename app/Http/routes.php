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
    return view('welcome');
});

/* ================== Homepage + Admin Routes ================== */

require __DIR__.'/admin_routes.php';

Route::auth();

Route::get('/home', 'HomeController@index');

Route::get('ajaxDefaultData', 'HomeController@ajaxDefaultData')->name('ajaxDefaultData');
Route::get('ajaxDataInPeriod', 'HomeController@ajaxDataInPeriod')->name('ajaxDataInPeriod');

