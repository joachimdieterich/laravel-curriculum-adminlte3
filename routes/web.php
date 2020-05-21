<?php

Route::redirect('/', '/features');

Route::get('/features', 'OpenController@features')->name('features');

Route::get('/impressum', 'OpenController@impressum')->name('impressum');

Route::get('/terms', 'OpenController@terms')->name('terms');

Auth::routes(['register' => false]);


Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::post('achievements', 'AchievementController@store');
    
    Route::resource('categories', 'CategorieController');
   
    Route::get('certificates/list', 'CertificateController@list');
    //Route::get('certificates/generate', 'CertificateController@generate'); //for testing only
    Route::post('certificates/generate', 'CertificateController@generate');
    Route::resource('certificates', 'CertificateController');
    
    Route::post('contents/{content}/destroy', 'ContentController@destroy')->name('contents.destroy'); //has to be post (has parameters)
    Route::resource('contents', 'ContentController');
    
    /* courses */
    Route::get('courses/list', 'CourseController@list');
    Route::resource('courses', 'CourseController');
  
    /* country */
    Route::get('countries/{country}/states', 'CountryController@getStates')->name('countries.states');

    /* curricula */
    Route::post('curricula/enrol', 'CurriculumController@enrol')->name('curricula.enrol');
    Route::delete('curricula/expel', 'CurriculumController@expel')->name('curricula.expel');
    Route::get('curricula/list', 'CurriculumController@list');
    Route::get('curricula/import', 'CurriculumImportController@import')->name('curricula.import');
    Route::post('curricula/import/store', 'CurriculumImportController@store')->name('curricula.import.store');
    Route::get('curricula/{curriculum}/achievements', 'CurriculumController@showAchievements')->name('curricula.showAchievements');
    Route::post('curricula/{curriculum}/achievements', 'CurriculumController@getAchievements')->name('curricula.getAchievements');
    Route::get('curricula/{curriculum}/objectives', 'CurriculumController@getObjectives')->name('curricula.getObjectives');
    Route::get('curricula/{curriculum}/editOwner', 'CurriculumController@editOwner')->name('curricula.editOwner');
    Route::patch('curricula/{curriculum}/editOwner', 'CurriculumController@storeOwner')->name('curricula.storeOwner');
    Route::resource('curricula', 'CurriculumController');

    /* enablingObjectives */
    Route::get('enablingObjectives/{enablingObjective}/referenceSubscriptionSiblings', 'EnablingObjectiveController@referenceSubscriptionSiblings');
    Route::get('enablingObjectives/{enablingObjective}/quoteSubscriptions', 'EnablingObjectiveController@quoteSubscriptions');
    Route::resource('enablingObjectives', 'EnablingObjectiveController');
    /* enablingObjectiveSubscriptions */
    Route::resource('enablingObjectiveSubscriptions', 'EnablingObjectiveSubscriptionsController');

    /* grades */
    Route::get('grades/list', 'GradesController@list')->name('grades.list');
    Route::delete('grades/destroy', 'GradesController@massDestroy')->name('grades.massDestroy');
    Route::resource('grades', 'GradesController');

    /* Group */
    Route::post('groups/enrol', 'GroupsController@enrol')->name('groups.enrol');
    Route::delete('groups/expel', 'GroupsController@expel')->name('groups.expel');

    Route::delete('groups/massDestroy', 'GroupsController@massDestroy')->name('groups.massDestroy');
    Route::get('groups/list', 'GroupsController@list');
    Route::resource('groups', 'GroupsController');

    Route::get('groupList', 'GroupsController@groupList');
    Route::resource('groups', 'GroupsController');
    
    Route::resource('levels', 'LevelController');
    
    /* logbooks */
    Route::get('logbooks/list', 'LogbookController@list');
    Route::resource('logbooks', 'LogbookController');
    
    /* logbook entries */
    Route::resource('logbookEntries', 'LogbookEntryController');
   
    /* Messages */
    Route::get('messages', 'MessagesController@index')->name('messages');
    Route::get('messages/create', 'MessagesController@create')->name('messages.create');
    Route::post('messages', 'MessagesController@store')->name('messages.store');
    Route::get('messages/{id}', 'MessagesController@show')->name('messages.show');
    Route::put('messages/{id}', 'MessagesController@update')->name('messages.update');
   
    /* Navigators */ 
    Route::get('navigators/list', 'NavigatorController@list');
    Route::resource('navigators', 'NavigatorController');
    
    Route::get('navigators/{navigator}/{navigator_view}', 'NavigatorViewController@show')->name('navigator.view');
   
    /* Navigator Views */
    Route::resource('navigatorViews', 'NavigatorViewController');
    
    /* Navigator Views */
    Route::resource('navigatorItems', 'NavigatorItemController');
    
    /* media */
    
    Route::post('mediumSubscriptions/destroy', 'MediumSubscriptionController@destroySubscription');
    Route::resource('mediumSubscriptions', 'MediumSubscriptionController');
    Route::resource('media', 'MediumController');


    /* objectiveTypes */
    Route::resource('objectiveTypes', 'ObjectiveTypeController');

    /* Organization */
    Route::post('organizations/enrol', 'OrganizationsController@enrol')->name('organizations.enrol');
    Route::delete('organizations/expel', 'OrganizationsController@expel')->name('organizations.expel');

    Route::delete('organizations/massDestroy', 'OrganizationsController@massDestroy')->name('organizations.massDestroy');
    Route::get('organizations/list', 'OrganizationsController@list')->name('organizations.list');
    Route::resource('organizations', 'OrganizationsController');

    /* organizationtype */
    Route::get('organizationtypes/list', 'OrganizationTypesController@list')->name('organizationtypes.list');
    Route::delete('organizationtypes/destroy', 'OrganizationTypesController@massDestroy')->name('organizationtypes.massDestroy');
    Route::resource('organizationtypes', 'OrganizationTypesController');
   
    /* period */
    Route::get('periods/list', 'PeriodController@list')->name('periods.list');
    Route::resource('periods', 'PeriodController');
    
    /* permission */
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    Route::get('print/glossar/{glossar}', 'PrintController@glossar')->name('print.glossar');
    Route::get('print/content/{content}', 'PrintController@content')->name('print.content');
    Route::get('print/curriculum/{curriculum}', 'PrintController@curriculum')->name('print.curriculum');
    Route::get('print/curriculum/{curriculum}/references', 'PrintController@references')->name('print.references');
    
    Route::resource('progresses', 'ProgressController');
   
    Route::post('repositorySubscriptions/destroySubscription', 'RepositorySubscriptionController@destroySubscription')->name('repositorySubscriptions.destroySubscription');
    Route::post('repositorySubscriptions/searchRepository', 'RepositorySubscriptionController@searchRepository')->name('repositorySubscriptions.searchRepository');
    Route::post('repositorySubscriptions/getMedia', 'RepositorySubscriptionController@getMedia')->name('repositorySubscriptions.getMedia');
    Route::resource('repositorySubscriptions', 'RepositorySubscriptionController');
    /* Roles */
    Route::delete('roles/massDestroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::get('roles/list', 'RolesController@list')->name('roles.list');
    Route::resource('roles', 'RolesController');

    /* sharingLevels */
    Route::resource('sharingLevels', 'SharingLevelController');

    /* statusdefinitions  */
    Route::resource('statusdefinitions', 'StatusDefinitionController');

    /* tasks */
    Route::patch('tasks/{task}/complete', 'TaskController@complete')->name('tasks.complete');
    Route::get('tasks/{task}/activity', 'TaskController@activity')->name('tasks.activity');
    Route::resource('tasks', 'TaskController');
    /* terminalObjectives */
    Route::get('terminalObjectives/{terminalObjective}/referenceSubscriptionSiblings', 'TerminalObjectiveController@referenceSubscriptionSiblings');
    Route::get('terminalObjectives/{terminalObjective}/quoteSubscriptions', 'TerminalObjectiveController@quoteSubscriptions');
    Route::resource('terminalObjectives', 'TerminalObjectiveController');
    /* terminalObjectiveSubscriptions */
    Route::resource('terminalObjectiveSubscriptions', 'TerminalObjectiveSubscriptionsController');

    /* reference(Subscription)  */
    Route::resource('references', 'ReferenceController');
    Route::resource('referenceSubscriptions', 'ReferenceSubscriptionController');

    /* User */
    Route::delete('users/massDestroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::patch('users/massUpdate', 'UsersController@massUpdate')->name('users.massUpdate');
    Route::patch('users/setCurrentOrganization', 'UsersController@setCurrentOrganization')->name('users.setCurrentOrganization');
    Route::patch('users/setAvatar', 'UsersController@setAvatar')->name('users.setAvatar');

    Route::get('users/list', 'UsersController@list');
    Route::resource('users', 'UsersController');
});

//if ((env('APP_ENV') == 'local')){
//    Route::get('/phpinfo', function (){phpinfo();})->middleware('admin'); //available in local env and admin only
//}

if (env('GUEST_USER') !== null)
{
    Route::get('/guest', function ()
    {
        if (Auth::user() == null)       //if no user is authenticated authenticate guest
        {
            Auth::loginUsingId((env('GUEST_USER')), true);
        }
        if (\App\User::find(env('GUEST_USER'))->organizations()->first()->navigators()->first() != null) //use guests default navigator
        {
            return redirect('/navigators/'.\App\User::find(env('GUEST_USER'))->organizations()->first()->navigators()->first()->id);
        }
        else 
        {
            return redirect('/');
        }
    }); 
}
