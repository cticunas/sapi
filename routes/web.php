<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/', function () { return view('home'); })->middleware('auth');
//Auth::routes();
 Route::get('/', function () { return redirect('public'); });
//  Route::get('/public', function () { return view('public'); });
 Route::get('/public','HomeController@public');
 Route::get('/incentives_pdf','OutcomeController@incentives_pdf');
 Route::get('/login', function () { return view('login'); });
//Route::get('/', 'HomeController@index')->name('home');

// ########################   Exports   ########################
Route::get('/greetings',"HomeController@greetings");

Route::any('{all}', function () {return view('home');})->where(['all' => '.*']);