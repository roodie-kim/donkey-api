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

Route::get('/user', 'Auth\LoginController@user')->middleware('auth:api');
Route::post('/auth', 'Auth\LoginController@login');

Route::post('/users', 'Auth\RegisterController@store');
Route::post('/resetPasswordEmail', 'Auth\ResetPasswordController@sendEmail');
Route::post('/resetPassword', 'Auth\ResetPasswordController@resetPassword');

// config
Route::get('/config', 'Auth\ConfigController@index')->middleware('auth:api');
Route::post('/changeNickname', 'Auth\ConfigController@changeNickname')->middleware('auth:api');
Route::post('/changePassword', 'Auth\ConfigController@changePassword')->middleware('auth:api');

// posts
Route::get('/posts', 'PostsController@index');
Route::post('/posts', 'PostsController@store')->middleware('auth:api');
Route::get('/posts/{post}', 'PostsController@show');
Route::patch('/posts/{post}', 'PostsController@update')->middleware('auth:api');
Route::delete('/posts/{post}', 'PostsController@delete')->middleware('auth:api');
Route::get('/bestindex', 'PostsController@bestIndex');

// comments
Route::get('/comments', 'CommentsController@index');
Route::post('/comments', 'CommentsController@store')->middleware('auth:api');
Route::patch('/comments/{comment}', 'CommentsController@update')->middleware('auth:api');
Route::delete('/comments/{comment}', 'CommentsController@delete')->middleware('auth:api');

// votes
Route::post('/votes', 'VotesController@store')->middleware('auth:api');

// test
// images
Route::post('/images', 'ImagesController@store')->middleware('auth:api');
