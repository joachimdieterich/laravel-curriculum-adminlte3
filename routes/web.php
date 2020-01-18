<?php

//Route::redirect('/', '/login');

Route::redirect('/home', '/');

Route::get('/impressum', 'OpenController@impressum')->name('Impressum');

Route::get('/terms', 'OpenController@terms')->name('Terms');

Auth::routes(['register' => false]);


Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::post('achievements', 'AchievementController@store');
    
    Route::resource('categories', 'CategorieController');
   
    Route::get('certificates/list', 'CertificateController@list');
    //Route::get('certificates/generate', 'CertificateController@generate'); //for testing only
    Route::post('certificates/generate', 'CertificateController@generate');
    Route::resource('certificates', 'CertificateController');
    
    Route::post('contents/{content}/destroy', 'ContentController@destroy')->name('contents.destroy'); //has to be post (has parameters)
    Route::resource('contents', 'ContentController');

    /* courses routes */
    Route::get('courses/list', 'CourseController@list');
    Route::resource('courses', 'CourseController');
    
    /* country routes */
    Route::get('countries/{country}/states', 'CountryController@getStates')->name('countries.states');

    /* curricula routes */
    Route::post('curricula/enrol', 'CurriculumController@enrol')->name('curricula.enrol');
    Route::delete('curricula/expel', 'CurriculumController@expel')->name('curricula.expel');
    Route::get('curricula/list', 'CurriculumController@list');
    Route::get('curricula/import', 'CurriculumImportController@import')->name('curricula.import');
    Route::post('curricula/import/store', 'CurriculumImportController@store')->name('curricula.import.store');
    Route::get('curricula/{curriculum}/achievements', 'CurriculumController@showAchievements')->name('curricula.showAchievements');
    Route::post('curricula/{curriculum}/achievements', 'CurriculumController@getAchievements')->name('curricula.getAchievements');
    Route::resource('curricula', 'CurriculumController');

    /* enablingObjectives routes */
    Route::get('enablingObjectives/{enablingObjective}/referenceSubscriptionSiblings', 'EnablingObjectiveController@referenceSubscriptionSiblings');
    Route::get('enablingObjectives/{enablingObjective}/quoteSubscriptions', 'EnablingObjectiveController@quoteSubscriptions');
    Route::resource('enablingObjectives', 'EnablingObjectiveController');

    /* grades routes */
    Route::get('grades/list', 'GradesController@list')->name('grades.list');
    Route::delete('grades/destroy', 'GradesController@massDestroy')->name('grades.massDestroy');
    Route::resource('grades', 'GradesController');

    /* Group routes */
    Route::post('groups/enrol', 'GroupsController@enrol')->name('groups.enrol');
    Route::delete('groups/expel', 'GroupsController@expel')->name('groups.expel');

    Route::delete('groups/massDestroy', 'GroupsController@massDestroy')->name('groups.massDestroy');
    Route::get('groups/list', 'GroupsController@list');
    Route::resource('groups', 'GroupsController');

    Route::get('groupList', 'GroupsController@groupList');
    Route::resource('groups', 'GroupsController');
    
     Route::resource('levels', 'LevelController');

    /* Navigators */
    
    Route::get('navigators/list', 'NavigatorController@list');
    Route::resource('navigators', 'NavigatorController');
    
    Route::get('navigators/{navigator}/{navigator_view}', 'NavigatorViewController@show')->name('navigator.view');
   
    /* Navigator Views */
    Route::resource('navigatorViews', 'NavigatorViewController');
    
    /* Navigator Views */
    Route::resource('navigatorItems', 'NavigatorItemController');
    
    /* media routes */
    Route::resource('media', 'MediumController');


    /* objectiveTypes routes */
    Route::resource('objectiveTypes', 'ObjectiveTypeController');

    /* Organization routes */
    Route::post('organizations/enrol', 'OrganizationsController@enrol')->name('organizations.enrol');
    Route::delete('organizations/expel', 'OrganizationsController@expel')->name('organizations.expel');

    Route::delete('organizations/massDestroy', 'OrganizationsController@massDestroy')->name('organizations.massDestroy');
    Route::get('organizations/list', 'OrganizationsController@list')->name('organizations.list');
    Route::resource('organizations', 'OrganizationsController');

    /* organizationtype routes */
    Route::get('organizationtypes/list', 'OrganizationTypesController@list')->name('organizationtypes.list');
    Route::delete('organizationtypes/destroy', 'OrganizationTypesController@massDestroy')->name('organizationtypes.massDestroy');
    Route::resource('organizationtypes', 'OrganizationTypesController');
   
    /* period routes*/
    Route::get('periods/list', 'PeriodController@list')->name('periods.list');
    Route::resource('periods', 'PeriodController');
    
    /* permission routes */
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    Route::get('print/glossar/{glossar}', 'PrintController@glossar')->name('print.glossar');
    Route::get('print/content/{content}', 'PrintController@content')->name('print.content');
    Route::get('print/curriculum/{curriculum}', 'PrintController@curriculum')->name('print.curriculum');
    Route::get('print/curriculum/{curriculum}/references', 'PrintController@references')->name('print.references');
    
    Route::resource('progresses', 'ProgressController');
    /* Roles routes */
    Route::delete('roles/massDestroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::get('roles/list', 'RolesController@list')->name('roles.list');
    Route::resource('roles', 'RolesController');

    /* sharingLevels routes */
    Route::resource('sharingLevels', 'SharingLevelController');

    /* statuses routes */
    Route::resource('statuses', 'StatusController');

    /* terminalObjectives routes */
    Route::get('terminalObjectives/{terminalObjective}/referenceSubscriptionSiblings', 'TerminalObjectiveController@referenceSubscriptionSiblings');
    Route::get('terminalObjectives/{terminalObjective}/quoteSubscriptions', 'TerminalObjectiveController@quoteSubscriptions');
    Route::resource('terminalObjectives', 'TerminalObjectiveController');

    /* reference(Subscription) routes */
    Route::resource('references', 'ReferenceController');
    Route::resource('referenceSubscriptions', 'ReferenceSubscriptionController');

    /* User routes */
    Route::delete('users/massDestroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::patch('users/massUpdate', 'UsersController@massUpdate')->name('users.massUpdate');
    Route::patch('users/setCurrentOrganization', 'UsersController@setCurrentOrganization')->name('users.setCurrentOrganization');
    Route::patch('users/setAvatar', 'UsersController@setAvatar')->name('users.setAvatar');

    Route::get('users/list', 'UsersController@list');
    Route::resource('users', 'UsersController');
});

if ((env('APP_ENV') == 'local')){
    Route::get('/phpinfo', function (){phpinfo();}); //available in local env
}

if (env('GUEST_USER') !== null)
{
    Route::get('/guest', function ()
    {
        Auth::loginUsingId((env('GUEST_USER')), true);
        if (auth()->user()->organizations()->first()->navigators()->first() != null)
        {
            return redirect('/navigators/'.auth()->user()->organizations()->first()->navigators()->first()->id);
        }
        else 
        {
            return redirect('/');
        }
    }); 
}
