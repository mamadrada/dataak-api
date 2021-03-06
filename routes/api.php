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

Route::post('login', 'PassportController@login')->name('login');
Route::post('register', 'PassportController@register');

Route::middleware('auth:api')->group(function () {
    Route::resource('appointments', 'AppointmentController');
    Route::get('invitation','InviteController@invitaion');
    Route::post('appointment/{appointment}/invite','InviteController@invitePerson');
    Route::post('invitation/{invitation}/set-status','InviteController@setStatus');
    Route::get('invitation/{appointment}/list','InviteController@inviteList');
});
