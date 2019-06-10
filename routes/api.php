<?php

 use Illuminate\Http\Request;

 
Route::group(['prefix' => 'v1', 'namespace' => 'Api\V1'], function () { 
    Route::get('about', 'AboutApiController@index');
    //Route::apiResource('about', 'AboutApiController');
});
Route::group(['prefix' => 'v1', 'as' => 'admin.', 'namespace' => 'Api\V1\Admin'], function () {
    
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
    
    
    Route::apiResource('permissions', 'PermissionsApiController');

    Route::apiResource('roles', 'RolesApiController');

    Route::apiResource('users', 'UsersApiController');

    Route::apiResource('organizations', 'OrganizationsApiController');
});
