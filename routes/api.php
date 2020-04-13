<?php

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

// Health Check
Route::get('/healthcheck', function () {
    return response()->json([
        'api_version' => 1.0,
        'alive'   => 'Hell Yeah...',
        'timestamp'  => Carbon::now()->timestamp
    ]);
});

// Users - Auth
Route::post('/users/login', 'AuthController@login');
Route::post('/users/logout', 'AuthController@logout');
Route::post('/users/register', 'AuthController@register');
Route::get('/users/me', 'AuthController@getAuthUser');

// Users
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