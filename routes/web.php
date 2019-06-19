<?php

Route::redirect('/', '/login');

Route::redirect('/home', '/admin');


Route::get('/impressum', 'OpenController@impressum')->name('Impressum');
Route::get('/terms', 'OpenController@terms')->name('Terms');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    
    /* Organization routes */
    Route::post('organizations/enrol', 'OrganizationsController@enrol')->name('organizations.enrol');
    Route::delete('organizations/expel', 'OrganizationsController@expel')->name('organizations.expel');
    
    Route::delete('organizations/massDestroy', 'OrganizationsController@massDestroy')->name('organizations.massDestroy');
    Route::get('organizations/list', 'OrganizationsController@list')->name('organizations.list');
    Route::resource('organizations', 'OrganizationsController');
    
   
    /* Role routes */
    Route::delete('roles/massDestroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::get('roles/list', 'RolesController@list')->name('roles.list');
    Route::resource('roles', 'RolesController');
    
    
    /* User routes */
    Route::delete('users/massDestroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::patch('users/massUpdate', 'UsersController@massUpdate')->name('users.massUpdate');
    Route::patch('users/setCurrentOrganization', 'UsersController@setCurrentOrganization')->name('users.setCurrentOrganization');
    
    Route::get('users/list', 'UsersController@list');
    Route::resource('users', 'UsersController');
   
    /* Group routes */
    Route::post('groups/enrol', 'GroupsController@enrol')->name('groups.enrol');
    Route::delete('groups/expel', 'GroupsController@expel')->name('groups.expel');
    
    Route::delete('groups/massDestroy', 'GroupsController@massDestroy')->name('groups.massDestroy');
    Route::get('groups/list', 'GroupsController@list');
    Route::resource('groups', 'GroupsController');
    
    
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');

    Route::resource('permissions', 'PermissionsController');

    Route::get('groupList', 'GroupsController@groupList');
    Route::resource('groups', 'GroupsController');
    
    
    
    Route::delete('grades/destroy', 'GradesController@massDestroy')->name('grades.massDestroy');
    
    Route::resource('grades', 'GradesController');
    
    Route::get('organizationTypeList', 'OrganizationTypesController@organizationTypeList');
    Route::resource('organizationtypes', 'OrganizationTypesController');
    
    Route::delete('organizationtypes/destroy', 'OrganizationTypesController@massDestroy')->name('organizationtypes.massDestroy');
    
    
    

    
});

Route::group(['middleware' => 'auth'], function () {
    Route::resource('contents', 'ContentController');
    
    /* curricula routes */
    Route::resource('curricula', 'CurriculumController');
    Route::get('curriculaList', 'CurriculumController@curriculaList');
  
    
});