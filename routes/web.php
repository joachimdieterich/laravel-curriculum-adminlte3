<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/home');
//Route::get('/wip', 'OpenController@wip')->name('wip');
Route::get('/features', 'OpenController@features')->name('features');

Route::get('/impressum', 'OpenController@impressum')->name('impressum');

Route::get('/terms', 'OpenController@terms')->name('terms');

Route::get('/localLogin', 'Auth\LoginController@localLogin');
Route::get('/localLogout', 'Auth\LoginController@localLogout');
Auth::routes(['register' => false]);

Route::get('eventSubscriptions/embed', 'EventSubscriptionController@embed')->name('eventSubscriptions.embed'); //embeddable routes
Route::get('videoconferences/endCallback', 'VideoconferenceController@endCallback'); // called via bbb server (with meeting id)

Route::group(['middleware' => 'auth'], function () {
    //LogController::setStatistics(); //to slow -> use queue instead
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/admin', 'AdminController@index')->name('admin.index');

    Route::resource('calendarEvents', 'CalendarEventController');

    Route::post('kanbanItemComments/{kanbanItemComment}/react', 'KanbanItemCommentController@reaction')->name('kanbanItemCommentController.react');
    Route::resource('kanbanItemComment', 'KanbanItemCommentController');

    Route::resource('agendas', 'AgendaController');
    Route::resource('agendaItems', 'AgendaItemController');
    Route::resource('agendaItemTypes', 'AgendaItemTypeController');
    Route::resource('agendaItemSubscriptions', 'AgendaItemSubscriptionController');
    Route::resource('agendaItemSpeakers', 'AgendaItemSpeakerController');

    Route::resource('absences', 'AbsenceController');

    Route::post('achievements', 'AchievementController@store');
    Route::get('achievements/enabling', 'AchievementController@getEnablingAchievements');

    Route::post('artefacts/destroy', 'ArtefactController@destroySubscription');
    Route::resource('artefacts', 'ArtefactController');

    Route::resource('categories', 'CategorieController');

    Route::get('certificates/list', 'CertificateController@list');
    //Route::get('certificates/generate', 'CertificateController@generate'); //for testing only
    Route::post('certificates/generate', 'CertificateController@generate');
    Route::resource('certificates', 'CertificateController');

    Route::get('configs/list', 'ConfigController@list');
    Route::get('configs/models', 'ConfigController@models');
    Route::resource('configs', 'ConfigController');

    Route::resource('contactdetails', 'ContactDetailController');

    Route::post('contents/{content}/destroy', 'ContentController@destroy'); //has to be post (has parameters)
    Route::resource('contents', 'ContentController');
    Route::patch('contentSubscriptions', 'ContentSubscriptionController@update');
    Route::patch('contentSubscriptions/reset', 'ContentSubscriptionController@reset');
    Route::resource('contentSubscriptions', 'ContentSubscriptionController');

    /* courses */
    Route::get('courses/list', 'CourseController@list');
    Route::resource('courses', 'CourseController');

    /* country */
    Route::get('countries/{country}/states', 'CountryController@getStates')->name('countries.states');
    Route::resource('countries', 'CountryController');

    /* curricula */
    Route::get('curricula/{curriculum}/variantDefinitions', 'CurriculumController@getVariantDefinitions');
    Route::put('curricula/{curriculum}/variantDefinitions', 'CurriculumController@setVariantDefinitions');
    Route::post('curricula/enrol', 'CurriculumController@enrol')->name('curricula.enrol');
    Route::delete('curricula/expel', 'CurriculumController@expel')->name('curricula.expel');
    Route::get('curricula/list', 'CurriculumController@list');
    Route::get('curricula/types', 'CurriculumController@types');
    Route::get('curricula/import', 'CurriculumImportController@import')->name('curricula.import');
    Route::post('curricula/import/store', 'CurriculumImportController@store')->name('curricula.import.store');
    Route::get('curricula/{curriculum}/export', 'CurriculumExportController@export')->name('curricula.export');
    Route::get('curricula/references', 'CurriculumController@references');
    Route::get('curricula/{curriculum}/achievements', 'CurriculumController@showAchievements')->name('curricula.showAchievements');
    Route::post('curricula/{curriculum}/achievements', 'CurriculumController@getAchievements')->name('curricula.getAchievements');
    Route::get('curricula/{curriculum}/objectives', 'CurriculumController@getObjectives')->name('curricula.getObjectives');
    Route::get('curricula/{curriculum}/editOwner', 'CurriculumController@editOwner')->name('curricula.editOwner');
    Route::patch('curricula/{curriculum}/editOwner', 'CurriculumController@storeOwner')->name('curricula.storeOwner');
    Route::patch('curricula/{curriculum}/resetOrderIds', 'CurriculumController@resetOrderIds')->name('curricula.resetOrderIds');
    Route::get('curricula/{curriculum}/print', 'CurriculumController@print')->name('curricula.print');
    Route::put('curricula/{curriculum}/syncObjectiveTypesOrder', 'CurriculumController@syncObjectiveTypesOrder')->name('curricula.syncObjectiveTypesOrder');
    Route::resource('curricula', 'CurriculumController');
    Route::resource('curriculumSubscriptions', 'CurriculumSubscriptionController');

    /* enablingObjectives */
    Route::get('enablingObjectives/{enablingObjective}/referenceSubscriptionSiblings', 'EnablingObjectiveController@referenceSubscriptionSiblings');
    Route::get('enablingObjectives/{enablingObjective}/quoteSubscriptions', 'EnablingObjectiveController@quoteSubscriptions');
    Route::get('enablingObjectives/{enablingObjective}/achievements/{group?}', 'EnablingObjectiveController@showAchievements')->name('enablingObjectives.showAchievements');
    Route::resource('enablingObjectives', 'EnablingObjectiveController');
    /* enablingObjectiveSubscriptions */
    Route::post('enablingObjectiveSubscriptions/destroy', 'EnablingObjectiveSubscriptionsController@destroySubscription');
    Route::resource('enablingObjectiveSubscriptions', 'EnablingObjectiveSubscriptionsController');

    /* plugin eventmanagment */
    Route::post('eventSubscriptions/destroySubscription', 'EventSubscriptionController@destroySubscription')->name('eventSubscriptions.destroySubscription');
    Route::post('eventSubscriptions/search', 'EventSubscriptionController@search')->name('eventSubscriptions.search');
    Route::post('eventSubscriptions/getEvents', 'EventSubscriptionController@getEvents')->name('eventSubscriptions.getEvents');

    Route::resource('exercises', 'ExerciseController');
    Route::resource('exerciseDones', 'ExerciseDoneController');

    Route::resource('eventSubscriptions', 'EventSubscriptionController');

    Route::resource('glossar', 'GlossarController');
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

    Route::get('kanbans/list', 'KanbanController@list');
    Route::get('kanbans/{kanban}/copy', 'KanbanController@copyKanban');
    Route::get('export_csv/{kanban}', 'KanbanController@exportKanbanCsv');
    Route::get('export_pdf/{kanban}', 'KanbanController@exportKanbanPdf');
    Route::resource('kanbans', 'KanbanController');

    Route::post('kanbanItems/{kanbanItem}/react', 'KanbanItemController@reaction')->name('kanbanItems.react');
    Route::get('kanbanItems/{kanbanItem}/editors', 'KanbanItemController@editors')->name('kanbanItems.editors');
    Route::get('kanbanItems/{item}/copy', 'KanbanItemController@copyItem');
    Route::put('kanbanItems/sync', 'KanbanItemController@sync')->name('kanbanItems.sync');
    Route::resource('kanbanItems', 'KanbanItemController');

    Route::get('kanbanStatuses/{kanban}/checkSync', 'KanbanStatusController@checkSync');
    Route::put('kanbanStatuses/sync', 'KanbanStatusController@sync')->name('kanbanStatuses.sync');
    Route::get('kanbanStatuses/{status}/copy', 'KanbanStatusController@copyStatus');
    Route::resource('kanbanStatuses', 'KanbanStatusController');

    Route::post('tokens', 'ShareTokenController@create');

    Route::get('kanbans/getColor/{id}', 'KanbanController@getColor');
    Route::post('kanbans/setColor', 'KanbanController@setColor');

    Route::resource('kanbanSubscriptions', 'KanbanSubscriptionController');

    Route::resource('kanbanItemSubscriptions', 'KanbanItemSubscriptionController');

    Route::resource('levels', 'LevelController');

    Route::resource('lmsReferenceSubscriptions', 'LmsReferenceSubscriptionController');

    Route::post('lmsReferences/get', 'LmsReferenceController@get')->name('lmsReferences.get');
    Route::resource('lmsReferences', 'LmsReferenceController');

    Route::resource('lmsUserTokens', 'LmsUserTokenController');

    /* logbooks */
    Route::get('logbooks/list', 'LogbookController@list');
    Route::get('logbooks/{logbook}/print', 'LogbookController@print')->name('logbooks.print');
    Route::resource('logbooks', 'LogbookController');
    Route::resource('logbookSubscriptions', 'LogbookSubscriptionController');

    /* logbook entries */
    Route::resource('logbookEntries', 'LogbookEntryController');
    Route::post('logbookEntries/setSubject', 'LogbookEntryController@setSubject');

    Route::get('maps/list', 'MapController@list');
    Route::resource('mapSubscriptions', 'MapSubscriptionController');

    Route::resource('maps', 'MapController');
    Route::resource('mapMarkers', 'MapMarkerController');
    Route::get('mapMarkerTypes/list', 'MapMarkerTypeController@list');
    Route::resource('mapMarkerTypes', 'MapMarkerTypeController');
    Route::resource('mapMarkerCategories', 'MapMarkerCategoryController');

    /* Metadataset */
    Route::get('metadatasets/list', 'MetadatasetController@list');
    Route::resource('metadatasets', 'MetadatasetController');

    /* Messages */
    Route::get('messages', 'MessagesController@index')->name('messages');
    Route::get('messages/create', 'MessagesController@create')->name('messages.create');
    Route::post('messages', 'MessagesController@store')->name('messages.store');
    Route::get('messages/{id}', 'MessagesController@show')->name('messages.show');
    Route::put('messages/{id}', 'MessagesController@update')->name('messages.update');
    Route::post('messages/{id}/destroy', 'MessagesController@destroy')->name('messages.destroy');

    /* Navigators */
    Route::get('navigators/list', 'NavigatorController@list');
    Route::resource('navigators', 'NavigatorController');

    Route::get('navigators/{navigator}/{navigator_view}', 'NavigatorViewController@show')->name('navigator.view');

    /* Navigator Views */
    Route::resource('navigatorViews', 'NavigatorViewController');

    /* Navigator Views */
    Route::resource('navigatorItems', 'NavigatorItemController');

    /* Notes */
    Route::resource('notes', 'NoteController');

    /* media */
    Route::post('mediumSubscriptions/destroy', 'MediumSubscriptionController@destroySubscription');
    Route::resource('mediumSubscriptions', 'MediumSubscriptionController');
    Route::delete('media/massDestroy', 'MediumController@massDestroy')->name('media.massDestroy');
    Route::get('media/list', 'MediumController@list')->name('media.list');
    Route::post('media/{medium}/destroy', 'MediumController@destroy'); //has to be post (has parameters)
    Route::get('media/{medium}/thumb', 'MediumController@thumb')->name('media.thumb');
    Route::resource('media', 'MediumController');

    Route::get('meetings/list', 'MeetingController@list')->name('meetings.list');
    Route::resource('meetings', 'MeetingController');

    Route::resource('meetingDates', 'MeetingDateController');

    /* objectiveTypes */
    Route::get('objectiveTypes/list', 'ObjectiveTypeController@list')->name('objectiveTypes.list');
    Route::resource('objectiveTypes', 'ObjectiveTypeController');

    /* Organization */
    Route::post('organizations/enrol', 'OrganizationsController@enrol')->name('organizations.enrol');
    Route::delete('organizations/expel', 'OrganizationsController@expel')->name('organizations.expel');

    Route::delete('organizations/massDestroy', 'OrganizationsController@massDestroy')->name('organizations.massDestroy');
    Route::get('organizations/list', 'OrganizationsController@list')->name('organizations.list');
    Route::get('organizations/{organization}/edit/address', 'OrganizationsController@editAddress')->name('organizations.editAddress');
    Route::get('organizations/{organization}/edit/lmsUrl', 'OrganizationsController@editLmsUrl')->name('organizations.editLmsUrl');
    Route::patch('organizations/{organization}/address', 'OrganizationsController@updateAddress')->name('organizations.updateAddress');
    Route::resource('organizations', 'OrganizationsController');

    /* organizationtype */
    Route::get('organizationtypes/list', 'OrganizationTypesController@list')->name('organizationtypes.list');
    Route::delete('organizationtypes/destroy', 'OrganizationTypesController@massDestroy')->name('organizationtypes.massDestroy');
    Route::resource('organizationtypes', 'OrganizationTypesController');

    /* period */
    Route::get('periods/list', 'PeriodController@list')->name('periods.list');
    Route::resource('periods', 'PeriodController');

    /* permission */
    Route::get('permissions/list', 'PermissionsController@list')->name('permissions.list');
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    Route::delete('plans/massDestroy', 'PlanController@massDestroy')->name('plans.massDestroy');
    Route::get('plans/list', 'PlanController@list');
    Route::get('plans/getTypes', 'PlanController@getTypes');
    Route::get('plans/{plan}/copy', 'PlanController@copyPlan');
    Route::get('plans/{plan}/getUsers', 'PlanController@getUsers');
    Route::put('plans/{plan}/syncEntriesOrder', 'PlanController@syncEntriesOrder');
    Route::get('plans/{plan}/getUserAchievements/{userIds}', 'PlanController@getUserAchievements');
    Route::resource('plans', 'PlanController');

    Route::resource('planSubscriptions', 'PlanSubscriptionController');
    Route::resource('planEntries', 'PlanEntryController');

    Route::resource('prerequisites', 'PrerequisitesController');

    Route::get('print/content/{content}', 'PrintController@content')->name('print.content');
    Route::get('print/glossar/{glossar}', 'PrintController@glossar')->name('print.glossar');
    Route::get('print/{model}/{id}', 'PrintController@model')->name('print.model');
    /*Route::get('print/curriculum/{curriculum}', 'PrintController@curriculum')->name('print.curriculum');*/
    Route::get('print/curriculum/{curriculum}/references', 'PrintController@references')->name('print.references');

    Route::resource('progresses', 'ProgressController');

    Route::post('repositorySubscriptions/destroySubscription', 'RepositorySubscriptionController@destroySubscription')->name('repositorySubscriptions.destroySubscription');
    Route::post('repositorySubscriptions/searchRepository', 'RepositorySubscriptionController@searchRepository')->name('repositorySubscriptions.searchRepository');
    Route::get('repositorySubscriptions/getMedia', 'RepositorySubscriptionController@getMedia')->name('repositorySubscriptions.getMedia');
    Route::resource('repositorySubscriptions', 'RepositorySubscriptionController');
    /* Roles */
    Route::get('roles/list', 'RolesController@list')->name('roles.list');
    Route::resource('roles', 'RolesController');

    /* sharingLevels */
    Route::resource('sharingLevels', 'SharingLevelController');

    Route::resource('statistics', 'StatisticController');
    /* statusdefinitions  */
    Route::resource('statusdefinitions', 'StatusDefinitionController');

    /* subjects  */
    Route::get('subjects/list', 'SubjectController@list')->name('subjects.list');
    Route::get('subjects/getSubject', 'SubjectController@getSubject');
    Route::resource('subjects', 'SubjectController');

    /* tasks */
    Route::patch('tasks/{task}/complete', 'TaskController@complete')->name('tasks.complete');
    Route::get('tasks/{task}/activity', 'TaskController@activity')->name('tasks.activity');
    Route::resource('tasks', 'TaskController');
    /* task(Subscriptions)*/
    Route::resource('taskSubscriptions', 'TaskSubscriptionController');
    /* terminalObjectives */
    Route::get('terminalObjectives/{terminalObjective}/referenceSubscriptionSiblings', 'TerminalObjectiveController@referenceSubscriptionSiblings');
    Route::get('terminalObjectives/{terminalObjective}/quoteSubscriptions', 'TerminalObjectiveController@quoteSubscriptions');
    Route::resource('terminalObjectives', 'TerminalObjectiveController');
    /* terminalObjectiveSubscriptions */
    Route::post('terminalObjectiveSubscriptions/destroy', 'TerminalObjectiveSubscriptionsController@destroySubscription');
    Route::resource('terminalObjectiveSubscriptions', 'TerminalObjectiveSubscriptionsController');

    Route::resource('trainings', 'TrainingController');
    Route::resource('trainingSubscriptions', 'TrainingSubscriptionController');

    /* reference(Subscription)  */
    Route::resource('references', 'ReferenceController');
    Route::resource('referenceSubscriptions', 'ReferenceSubscriptionController');

    /* User */
    Route::delete('users/massDestroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::patch('users/massUpdate', 'UsersController@massUpdate')->name('users.massUpdate');
    Route::patch('users/setCurrentOrganization', 'UsersController@setCurrentOrganization')->name('users.setCurrentOrganization');
    Route::patch('users/setCurrentPeriod', 'UsersController@setCurrentPeriod')->name('users.setCurrentPeriod');
    Route::patch('users/setAvatar', 'UsersController@setAvatar')->name('users.setAvatar');
    Route::get('users/{user}/dsgvoExport', 'UsersController@dsgvoExport')->name('users.dsgvoExport');
    Route::get('users/import', 'UsersController@createImport')->name('users.createImport');
    Route::post('users/import', 'UsersController@storeImport')->name('users.storeImport');
    Route::get('users/list', 'UsersController@list');
    Route::get('users/current', 'UsersController@getCurrentUser')->name('users.getCurrentUser');
    Route::get('users/{user}/avatar', 'UsersController@getAvatar');
    Route::delete('users/{user}/forceDestroy', 'UsersController@forceDestroy')->name('users.forceDestroy');
    Route::resource('users', 'UsersController');

    Route::get('variantDefinitions/list', 'VariantDefinitionController@list');
    Route::resource('variantDefinitions', 'VariantDefinitionController');
    Route::resource('variants', 'VariantController');

    Route::get('videoconferences/list', 'VideoconferenceController@list');
    Route::get('videoconferences/servers', 'VideoconferenceController@servers');
    Route::resource('videoconferences', 'VideoconferenceController');
    Route::get('videoconferences/{videoconference}/getStatus', 'VideoconferenceController@getStatus');
    Route::get('videoconferences/{videoconference}/start', 'VideoconferenceController@start');


    Route::resource('videoconferenceSubscriptions', 'VideoconferenceSubscriptionController');

    /* Tests */
    Route::get('tests', 'Tests\TestController@index');
    /* Exams */
    Route::get('exams_subscribed', 'Tests\ExamController@authUserIndexExams')->name('exams.index');
    Route::get('exams', 'Tests\ExamController@index');
    Route::post('exams', 'Tests\ExamController@create');
    Route::delete('exams/{exam}', 'Tests\ExamController@delete')->middleware('can:test_delete');
    Route::get('exam/{exam}/edit', 'Tests\ExamController@show');
    Route::get('exam/{exam}/list', 'Tests\ExamController@listExamUsers');
    Route::get('exam/{exam}/users/list', 'Tests\ExamController@listAllUsers');
    Route::post('exam/{exam}/status', 'Tests\ExamController@getExamStatus');
    Route::post('exam/{exam}/users/enrol', 'Tests\ExamController@addUsers');
    Route::delete('exam/{exam}/users/expel', 'Tests\ExamController@removeUsers');
    Route::post('exam/{exam}/report', 'Tests\ExamController@getReport');
});

//if ((env('APP_ENV') == 'local')){
//    Route::get('/phpinfo', function (){phpinfo();})->middleware('admin'); //available in local env and admin only
//}

if (env('GUEST_USER') !== null) {
    Route::get('videoconferences/{videoconference}/token', 'VideoconferenceController@getVideoconferenceByToken');
    Route::get('videoconferences/{videoconference}/startWithPw', 'VideoconferenceController@show');
    Route::get('kanbans/{kanban}/token', 'KanbanController@getKanbanByToken');
    Route::get('maps/{map}/token', 'MapController@getMapByToken');
    Route::get('kanban/share/{token}', 'ShareTokenController@auth');

    Route::get('/guest', function () {
        if (Auth::user() == null) {       //if no user is authenticated authenticate guest
            LogController::set('guestLogin');
            LogController::setStatistics();
            Auth::loginUsingId((env('GUEST_USER')), true);
        }
        if (\App\User::find(env('GUEST_USER'))->organizations()->first()->navigators()->first() != null) { //use guests default navigator
            return redirect('/navigators/' . \App\User::find(env('GUEST_USER'))->organizations()->first()->navigators()->first()->id);
        } else {
            return redirect('/');
        }
    });
}
