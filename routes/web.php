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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/**
 * Group routes
 * @author dplazao
 */
Route::get('/group/list', 'GroupController@list')->name('group.list');

Route::get('/group/create', function () {
    return view('group.create');
})->name('group.create')->middleware('auth');
Route::post('/group/createGroup', 'GroupController@create')->name('group.createGroup')->middleware('auth');

Route::get('/group/view/{groupID}', 'GroupController@view')->name('group.view');

Route::get('/group/join/{groupID}', 'GroupController@join')->name('group.join')->middleware('auth');

Route::get('/group/leave/{groupID}', 'GroupController@leave')->name('group.leave')->middleware('auth');

Route::get('/group/edit/{groupID}', 'GroupController@editView')->name('group.editView')->middleware('auth');
Route::post('/group/edit/{groupID}', 'GroupController@editAction')->name('group.editAction')->middleware('auth');

Route::get('/group/delete/{groupID}', 'GroupController@deleteView')->name('group.deleteView')->middleware('auth');
Route::post('/group/delete/{groupID}', 'GroupController@deleteAction')->name('group.deleteAction')->middleware('auth');

Route::get('/group/transferOwnership/{groupID}/{memberID}', 'GroupController@transferOwnershipView')->name('group.transferOwnershipView')->middleware('auth');
Route::post('/group/transferOwnership/{groupID}/{memberID}', 'GroupController@transferOwnershipAction')->name('group.transferOwnershipAction')->middleware('auth');

Route::get('/group/acceptMember/{groupID}/{memberID}', 'GroupController@acceptMember')->name('group.acceptMember')->middleware('auth');
Route::get('/group/removeMember/{groupID}/{memberID}', 'GroupController@removeMember')->name('group.removeMember')->middleware('auth');


/**
 * Post routes
 * @author ruch
 */
Route::get('/post/list', 'PostController@list')->name('post.list');

Route::get('/post/create', function () {
    return view('post.create');
})->name('post.create')->middleware('auth');
Route::post('/post/createPost', 'PostController@create')->name('post.createPost')->middleware('auth');

Route::get('/post/view/{postID}', 'PostController@view')->name('post.view');
