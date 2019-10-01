<?php

 use Illuminate\Http\Request;

Route::group([
    'prefix' => 'v1',  
    'namespace' => 'Api\V1'
    ], function () { 
    Route::get('about', 'AboutApiController@index');
    
    
});
/**
* Auth Routes
*/
Route::group([
    'prefix' => 'v1', 
    'as' => 'auth.', 
    'namespace' => 'Api\V1\Auth'
    ], function () { 
    
    
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
  
    Route::group([
      'middleware' => 'client_credentials'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});


Route::group([
    'prefix' => 'v1', 
    'as' => 'admin.', 
    'namespace' => 'Api\V1\Admin',
    'middleware' => 'client_credentials'
    ], function () {
    Route::apiResource('permissions', 'PermissionsApiController');

    Route::apiResource('roles', 'RolesApiController');
    
    Route::get('users/{user}/dashboard', 'UsersApiController@dashboard');
    Route::get('users/{user}/groups', 'UsersApiController@withGroups');
    Route::get('users/{user}/organizations', 'UsersApiController@withOrganizations');
    Route::get('users/{user}/roles', 'UsersApiController@withRoles');
    Route::apiResource('users', 'UsersApiController');

    Route::put('organizations/enrol', 'OrganizationsApiController@enrol');
    Route::delete('organizations/expel', 'OrganizationsApiController@expel');
    Route::apiResource('organizations', 'OrganizationsApiController');
    
    Route::apiResource('organizationtypes', 'OrganizationTypesApiController');
    
    Route::apiResource('grades', 'GradesApiController');
    
    Route::apiResource('periods', 'PeriodsApiController');
    
    Route::apiResource('subjects', 'SubjectsApiController');
    
    Route::put('groups/enrol', 'GroupsApiController@enrol');
    Route::delete('groups/expel', 'GroupsApiController@expel');
    Route::apiResource('groups', 'GroupsApiController');
    
    Route::apiResource('countries', 'CountriesApiController');
    Route::apiResource('states', 'StatesApiController');
});
    