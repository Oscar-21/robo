<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('storeProduct', 'ProductsController@store');
Route::get('getProduct', 'ProductsController@index');
Route::get('showProduct/{id}', 'ProductsController@show');
Route::post('deleteProduct/{id}', 'ProductsController@destroy');
Route::any('{path?}', 'MainController@index')->where("path", ".+");
