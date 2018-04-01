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

Auth::routes();

Route::get('/', 'Product@index');
Route::get('/category/{id}', 'Product@filterByCategory');
Route::get('/wishes', 'Product@wishes');
Route::get('/add/{id}', 'Product@addList');
Route::get('/remove/{id}', 'Product@removeList');
Route::get('/share', 'Share@index');
Route::post('/new_sharee', 'Share@newSharee');
Route::get('/remove_sharee/{id}', 'Share@removeSharee');
Route::get('/sharers', 'Share@sharers');
Route::get('/shared_list/{id}', 'Product@seeShare');
Route::get('/remove_shared/{product_id}/{sharer_id}', 'Product@removeShared');