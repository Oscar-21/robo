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

// Roles Routes
Route::post('storeRole', 'RolesController@store');
Route::get('getRoles', 'RolesController@index');
Route::post('updateRole/{id}', 'RolesController@update');
Route::get('showRole/{id}', 'RolesController@show');
Route::post('deleteRole/{id}', 'RolesController@destroy');

// Product Routes
Route::post('storeProduct', 'ProductsController@storeNewProduct');
Route::get('getProduct', 'ProductsController@index');
Route::get('showProduct/{id}', 'ProductsController@show');
Route::post('deleteProduct/{id}', 'ProductsController@destroy');
Route::post('toggleDescription/{id}', 'ProductsController@toggleDescription');
Route::post('togglePrice/{id}', 'ProductsController@togglePrice');
Route::post('toggleImage/{id}', 'ProductsController@toggleImage');
Route::post('toggleName/{id}', 'ProductsController@toggleName');
Route::post('toggleCategoryId/{id}', 'ProductsController@toggleCategoryId');
Route::post('toggleQuantity/{id}', 'ProductsController@toggleQuantity');
Route::get('toggleAvailability/{id}', 'ProductsController@toggleAvailability');

// User Routes
Route::post('signUp', 'UsersController@signUp');
Route::post('signIn', 'UsersController@signIn');
Route::get('showAllUsers', 'UsersController@index');
Route::post('updateAddress', 'UsersController@updateAddress');  
Route::post('deleteUser/{id}', 'UsersController@deleteUser');
Route::get('adminShowUser/{id}', 'UsersController@adminShowUser');
Route::get('userShow', 'UsersController@userShow');

// Order Routes
Route::post('storeOrder', 'OrdersController@store');

// Redirect Route
Route::any('{path?}', 'MainController@index')->where("path", ".+");
