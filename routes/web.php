<?php

Route::redirect('/', '/login');

Route::redirect('/home', '/admin');


Route::get('/impressum', 'OpenController@impressum')->name('Impressum');
Route::get('/terms', 'OpenController@terms')->name('Terms');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');

    Route::resource('permissions', 'PermissionsController');

    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');

    Route::resource('roles', 'RolesController');

    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::delete('users/massUpdate', 'UsersController@massUpdate')->name('users.massUpdate');
    
    Route::post('users/{user}/organization/enrol', 'UsersController@enrolToOrganization');
    Route::post('users/{user}/organization/{organization}/expel', 'UsersController@expelFromOrganization');
    
    Route::resource('users', 'UsersController');

    Route::delete('products/destroy', 'ProductsController@massDestroy')->name('products.massDestroy');

    Route::resource('products', 'ProductsController');
    
    Route::delete('organizations/destroy', 'OrganizationsController@massDestroy')->name('organizations.massDestroy');
    
    Route::resource('organizations', 'OrganizationsController');
    
    Route::delete('grades/destroy', 'GradesController@massDestroy')->name('grades.massDestroy');
    
    Route::resource('grades', 'GradesController');
    
    Route::get('organizationTypeList', 'OrganizationTypesController@organizationTypeList');
    Route::resource('organizationtypes', 'OrganizationTypesController');
    
    Route::delete('organizationtypes/destroy', 'OrganizationTypesController@massDestroy')->name('organizationtypes.massDestroy');
    
});

Route::group(['middleware' => 'auth'], function () {
    Route::resource('contents', 'ContentController');
});