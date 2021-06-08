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
//
//Route::get('/', function () {
//    return view('/customer.customerDetails');
//})->middleware('auth');

Auth::routes();

Route::get('/', 'HomeController@customerList')->name('customerDetails ')->middleware(['auth', 'CheckAdminAuth']);
Route::get('/customer-details', 'HomeController@customerList')->name('customerDetails')->middleware(['auth', 'CheckAdminAuth']);
Route::get('/new-customer', 'HomeController@newCustomer')->name('newCustomer')->middleware(['auth', 'CheckAdminAuth']);
Route::get('/detail/{id}', 'HomeController@detail')->name('details')->middleware(['auth', 'CheckAdminAuth']);
Route::get('/approved/{id}', 'HomeController@approvedCustomer')->name('approved')->middleware(['auth', 'CheckAdminAuth']);
Route::get('/reject/{id}', 'HomeController@rejectCustomer')->name('reject')->middleware(['auth', 'CheckAdminAuth']);
Route::get('/customer-list', 'HomeController@customerList')->name('list')->middleware(['auth', 'CheckAdminAuth']);
Route::get('/unauthorised', 'HomeController@unauthorised')->name('unauthorised')->middleware(['auth', 'CheckAdminAuth']);
Route::post('/reset/count', 'HomeController@resetUserCount')->name('reset.count')->middleware(['auth', 'CheckAdminAuth']);
//Route::post('/time_settings', 'HomeController@timeSetting')->name('time')->middleware(['auth', 'CheckAdminAuth']);
Route::post('/save/counter', 'HomeController@saveCount')->name('saveCounter')->middleware(['auth', 'CheckAdminAuth']);

//Route::group(['prefix' => '/customer', 'namespace' => 'customer'], function(){
//
//});

