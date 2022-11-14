<?php

Route::group([
    'prefix' => 'v1',
    'namespace' => 'Api\V1',
], function () {
    Route::get('about', 'AboutApiController@index');
});
/**
 * Auth Routes
 */
Route::group([
    'prefix' => 'v1',
    'as' => 'auth.',
    'namespace' => 'Api\V1\Auth',
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
        'middleware' => 'client_credentials',
    ], function () {
            Route::get('logout', 'AuthController@logout');
            Route::get('user', 'AuthController@user');
        });
});

/**
 * metadatasets Get Parameter 'password' is required, 'metadata_password' has to be in configs table.
 * INSERT INTO `configs` ( `key`, `value`, `referenceable_type`, `referenceable_id`, `data_type`, `created_at`, `updated_at`) VALUES
    ( 'metadata_password', 'yourPassword', NULL, NULL, 'string', '2020-07-15 11:18:21', '2020-07-15 11:18:21');
 */
Route::group([
    'prefix' => 'v1',
    'as' => 'admin.',
    'namespace' => 'Api\V1\Admin',
    'middleware' => 'throttle:10,10', // an IP can call endpoint 10 times in 10 minutes
], function () {
    Route::get('curricula/metadatasets', 'CurriculaApiController@getAllMetadatasets');
    Route::get('curricula/{curriculum}/metadataset', 'CurriculaApiController@getSingleMetadataset');
});

Route::group([
    'prefix' => 'v1',
    'as' => 'admin.',
    'namespace' => 'Api\V1\Admin',
    'middleware' => 'client_credentials',
], function () {

//    Route::get('curricula/metadatasets', 'CurriculaApiController@getAllMetadatasets');
//    Route::get('curricula/{curriculum}/metadataset', 'CurriculaApiController@getSingleMetadataset');
    Route::apiResource('curricula', 'CurriculaApiController');
    Route::apiResource('permissions', 'PermissionsApiController');

    Route::apiResource('roles', 'RolesApiController');

    Route::get('users/{user}/dashboard', 'UsersApiController@dashboard');
    Route::get('users/{user}/groups', 'UsersApiController@withGroups');
    Route::get('users/{user}/organizations', 'UsersApiController@withOrganizations');
    Route::get('users/{user}/roles', 'UsersApiController@withRoles');
    Route::delete('users/{user}/force', 'UsersApiController@forceDestroy');
    Route::apiResource('users', 'UsersApiController');

    Route::put('organizations/enrol', 'OrganizationsApiController@enrol');
    Route::delete('organizations/expel', 'OrganizationsApiController@expel');
    Route::get('organizations/{organization}/members', 'OrganizationsApiController@members');
    Route::apiResource('organizations', 'OrganizationsApiController');

    Route::apiResource('organizationtypes', 'OrganizationTypesApiController');

    Route::apiResource('grades', 'GradesApiController');

    Route::apiResource('periods', 'PeriodsApiController');

    Route::apiResource('subjects', 'SubjectsApiController');

    Route::put('groups/enrol', 'GroupsApiController@enrol');
    Route::delete('groups/expel', 'GroupsApiController@expel');
    Route::get('groups/{group}/members', 'GroupsApiController@members');
    Route::apiResource('groups', 'GroupsApiController');

    Route::apiResource('countries', 'CountriesApiController');
    Route::apiResource('states', 'StatesApiController');

    Route::get('moodle/getModelTypes', 'MoodleApiController@getModelTypes');
    Route::get('moodle/curricula', 'MoodleApiController@getCurricula');
    Route::get('moodle/curricula/{curriculum}/terminalObjectives', 'MoodleApiController@getTerminalObjectives');
    Route::get('moodle/curricula/terminalObjectives/{terminalObjective}/enablingObjectives', 'MoodleApiController@getEnablingObjectivesByTerminalObjectiveId');
    Route::get('moodle/curricula/{curriculum}/enablingObjectives', 'MoodleApiController@getEnablingObjectives');
    Route::get('moodle/logbooks', 'MoodleApiController@getLogbooks');
    Route::get('moodle/kanbans', 'MoodleApiController@getKanbans');
});
