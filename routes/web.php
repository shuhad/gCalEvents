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


Route::get('/', 'gCalendarController@events');

Route::get('events', 'gCalendarController@events');

// Route::get('events', function () {
//     session_start();
//     return view('gCalendarController@event');
//     //return view('events.index');
// });
Route::resource('gcalendar', 'gCalendarController');
Route::get('/api/oauth', 'gCalendarController@oauth')->name('oauthCallback');
Route::resource('/api/cal', 'gCalendarController');
Route::get('logout', 'gCalendarController@logout');

