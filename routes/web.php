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
 * @author dplazao 40132793
 */
Route::prefix('group')->group(function () {
    Route::get('list', 'GroupController@list')->name('group.list');

    Route::view('create','group.create')->name('group.create')->middleware('auth');
    Route::post('createGroup', 'GroupController@create')->name('group.createGroup')->middleware('auth');

    Route::get('view/{groupID}', 'GroupController@view')->name('group.view');

    Route::get('join/{groupID}', 'GroupController@join')->name('group.join')->middleware('auth');

    Route::get('leave/{groupID}', 'GroupController@leave')->name('group.leave')->middleware('auth');

    Route::get('edit/{groupID}', 'GroupController@editView')->name('group.editView')->middleware('auth');
    Route::post('edit/{groupID}', 'GroupController@editAction')->name('group.editAction')->middleware('auth');

    Route::get('delete/{groupID}', 'GroupController@deleteView')->name('group.deleteView')->middleware('auth');
    Route::post('delete/{groupID}', 'GroupController@deleteAction')->name('group.deleteAction')->middleware('auth');

    Route::get('transferOwnership/{groupID}/{memberID}', 'GroupController@transferOwnershipView')->name('group.transferOwnershipView')->middleware('auth');
    Route::post('transferOwnership/{groupID}/{memberID}', 'GroupController@transferOwnershipAction')->name('group.transferOwnershipAction')->middleware('auth');

    Route::get('acceptMember/{groupID}/{memberID}', 'GroupController@acceptMember')->name('group.acceptMember')->middleware('auth');
    Route::get('removeMember/{groupID}/{memberID}', 'GroupController@removeMember')->name('group.removeMember')->middleware('auth');
});

/** User routes @author Annes Cherid 40038453*/
Route::get('/users', function () {
    return view('users.view');
})->name('users.view')->middleware('auth');
Route::get('/users/list', 'UserController@listAllUsers')->name('users.list')->middleware('auth');
Route::post('/users/createUser', 'UserController@createUser')->name('users.createUser')->middleware('auth')->middleware('can:sysadmin');
Route::post('/users/deleteUser', 'UserController@deleteUser')->name('users.deleteUser')->middleware('auth')->middleware('can:sysadmin');
Route::post('/users/editUser', 'UserController@editUser')->name('users.editUser')->middleware('auth');

/**
 * Association routes
 * @author dplazao 40132793
 */
Route::group(['prefix' => 'association', 'as' => 'association.'], function () {
    Route::get('list', 'AssociationController@list')->name('list')->middleware('auth')->middleware('can:sysadmin');
    Route::get('view/{associationID}', 'AssociationController@view')->name('view')->middleware('auth')->middleware('can:view-association,associationID');
    Route::view('create', 'association.create')->name('create')->middleware('auth')->middleware('can:sysadmin');
    Route::post('createAssociation', 'AssociationController@createAction')->name('createAction')->middleware('auth')->middleware('can:sysadmin');
    Route::get('createMember/{associationID}', 'AssociationController@createMemberView')->name('createMemberView')->middleware('auth')->middleware('can:modify-association,associationID');
    Route::post('createMember/{associationID}', 'AssociationController@createMemberAction')->name('createMemberAction')->middleware('auth')->middleware('can:modify-association,associationID');
    Route::get('removeMember/{associationID}/{memberID}', 'AssociationController@removeMember')->name('removeMember')->middleware('auth')->middleware('can:modify-association,associationID');
    Route::get('edit/{associationID}', 'AssociationController@editView')->name('editView')->middleware('auth')->middleware('can:modify-association,associationID');
    Route::post('edit/{associationID}', 'AssociationController@editAction')->name('editAction')->middleware('auth')->middleware('can:modify-association,associationID');
    Route::get('delete/{associationID}', 'AssociationController@deleteView')->name('deleteView')->middleware('auth')->middleware('can:sysadmin');
    Route::post('delete/{associationID}', 'AssociationController@deleteAction')->name('deleteAction')->middleware('auth')->middleware('can:sysadmin');
});

/**
 * Building routes
 * @author dplazao 40132793
 */
Route::group(['prefix' => 'building', 'as' => 'building.'], function () {
    Route::get('list', 'BuildingController@list')->name('list')->middleware('auth');
    Route::get('view/{buildingID}', 'BuildingController@view')->name('view')->middleware('auth')->middleware('can:view-building,buildingID');

    Route::view('create', 'building.create')->name('create')->middleware('auth')->middleware('can:sysadmin');
    Route::post('createBuilding', 'BuildingController@createAction')->name('createAction')->middleware('auth')->middleware('can:sysadmin');
    Route::get('edit/{buildingID}', 'BuildingController@editView')->name('editView')->middleware('auth')->middleware('can:modify-building,buildingID');
    Route::post('edit/{buildingID}', 'BuildingController@editAction')->name('editAction')->middleware('auth')->middleware('can:modify-building,buildingID');
    Route::get('delete/{buildingID}', 'BuildingController@deleteView')->name('deleteView')->middleware('auth')->middleware('can:sysadmin');
    Route::post('delete/{buildingID}', 'BuildingController@deleteAction')->name('deleteAction')->middleware('auth')->middleware('can:sysadmin');

    Route::get('createCondo/{buildingID}', 'BuildingController@createCondoView')->name('createCondoView')->middleware('auth')->middleware('can:modify-building,buildingID');
    Route::post('createCondo/{buildingID}', 'BuildingController@createCondoAction')->name('createCondoAction')->middleware('auth')->middleware('can:modify-building,buildingID');
    Route::get('removeCondo/{buildingID}/{condoID}', 'BuildingController@removeCondo')->name('removeCondo')->middleware('auth')->middleware('can:modify-building,buildingID');

    Route::get('editCondo/{buildingID}/{condoID}', 'BuildingController@editCondoView')->name('editCondoView')->middleware('auth')->middleware('can:transfer-condo,buildingID,condoID');
    Route::post('editCondo/{buildingID}/{condoID}', 'BuildingController@editCondoAction')->name('editCondoAction')->middleware('auth')->middleware('can:transfer-condo,buildingID,condoID');
});

/**
 * Relationship routes
 * @author dplazao 40132793
 */
Route::group(['prefix' => 'relationship', 'as' => 'relationship.'], function () {
    Route::get('list', 'RelationshipController@list')->name('list')->middleware('auth');

    Route::view('create', 'relationship.create')->name('create')->middleware('auth');
    Route::post('createAction', 'RelationshipController@createAction')->name('createAction')->middleware('auth');

    Route::post('delete/{memberID}/{withMemberID}', 'RelationshipController@delete')->name('delete')->middleware('auth');
});
