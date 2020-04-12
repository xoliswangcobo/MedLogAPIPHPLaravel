<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Users - Auth
Route::get('/users/login', 'AuthController@login');
Route::get('/users/logout', 'AuthController@logout');
Route::get('/users/register', 'AuthController@register');

// Users
Route::get('/users/me', function() {});
Route::get('/users', function(Request $request) {
    return $request->isJson() ? 'Yes' : 'No';
});
Route::get('/users/{name}', function() {});
Route::get('/users/friends', function() {});
Route::post('/users/patients', function() {});
Route::post('/users/patients/me', function() {});
Route::post('/users/patients/{id}', function() {});

// Hospitals
Route::post('/hospitals/all', function() {});
Route::post('/hospitals/{name}}', function() {});

// Files and fileable