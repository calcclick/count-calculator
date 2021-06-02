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

Route::get('/', 'HomeController@customerList')->name('customerDetails ')->middleware('auth');
Route::get('/customer-details', 'HomeController@customerList')->name('customerDetails')->middleware('auth');
Route::get('/new-customer', 'HomeController@newCustomer')->name('newCustomer')->middleware('auth');
Route::get('/detail/{id}', 'HomeController@detail')->name('details')->middleware('auth');
Route::get('/approved/{id}', 'HomeController@approvedCustomer')->name('approved')->middleware('auth');
Route::get('/reject/{id}', 'HomeController@rejectCustomer')->name('reject')->middleware('auth');
Route::get('/customer-list', 'HomeController@customerList')->name('list')->middleware('auth');
Route::get('/unauthorised', 'HomeController@unauthorised')->name('unauthorised')->middleware('auth');
//Route::group(['prefix' => '/customer', 'namespace' => 'customer'], function(){
//
//});

