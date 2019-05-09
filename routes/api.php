<?php
  
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


Route::get('podcasts', 'API\PodcastsController@index');
Route::get('podcasts/{id}', 'API\PodcastsController@show');
Route::post('podcasts', 'API\PodcastsController@store');
Route::post('podcasts/{id}', 'API\PodcastsController@update');
Route::post('podcasts/approve/{id}', 'API\PodcastsController@approvel');
Route::delete('podcasts/{id}', 'API\PodcastsController@destroy');

Route::post('add-comment', 'API\PodcastsController@add_comment');
Route::post('flag-comment/{id}', 'API\PodcastsController@flag_comment');