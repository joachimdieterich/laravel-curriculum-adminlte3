<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/home');

Route::get('/features', 'OpenController@features')->name('features');
Route::get('/impressum', 'OpenController@impressum')->name('impressum');
Route::get('/terms', 'OpenController@terms')->name('terms');

Route::get('/localLogin', 'Auth\LoginController@localLogin');
Route::get('/localLogout', 'Auth\LoginController@localLogout');
Auth::routes(['register' => false]);

Route::get('eventSubscriptions/embed', 'EventSubscriptionController@embed')->name('eventSubscriptions.embed'); //embeddable routes
Route::get('videoconferences/endCallback', 'VideoconferenceController@endCallback'); // called via bbb server (with meeting id)

// don't authenticate requests that are made after the initial blade-file request
Route::withoutMiddleware('auth')->group(function() {
// A
    Route::post('achievements', 'AchievementController@store');

    Route::post('artefacts/destroy', 'ArtefactController@destroySubscription');
// B
// C
    Route::get('certificates/list', 'CertificateController@list');
    Route::post('certificates/generate', 'CertificateController@generate');

    Route::get('configs/list', 'ConfigController@list');
    Route::get('configs/models', 'ConfigController@models');

    Route::post('contents/{content}/destroy', 'ContentController@destroy'); // has to be post (has parameters)
    Route::patch('contentSubscriptions', 'ContentSubscriptionController@update');
    Route::patch('contentSubscriptions/reset', 'ContentSubscriptionController@reset');

    Route::get('courses/list', 'CourseController@list');
    Route::resource('courses', 'CourseController');

    Route::get('countries/{country}/states', 'CountryController@getStates')->name('countries.states');

    /*** Curricula ***/
    Route::post('curricula/enrol', 'CurriculumController@enrol')->name('curricula.enrol');
    Route::delete('curricula/expel', 'CurriculumController@expel')->name('curricula.expel');
    Route::get('curricula/list', 'CurriculumController@list');
    Route::get('curricula/types', 'CurriculumController@types');
    Route::get('curricula/references', 'CurriculumController@references');
    Route::get('curricula/{curriculum}', 'CurriculumController@show');
    Route::get('curricula/{curriculum}/achievements', 'CurriculumController@showAchievements')->name('curricula.showAchievements');
    Route::post('curricula/{curriculum}/achievements', 'CurriculumController@getAchievements')->name('curricula.getAchievements');
    Route::get('curricula/{curriculum}/certificates', 'CurriculumController@getCertificates')->name('curricula.getCertificates');
    Route::get('curricula/{curriculum}/editOwner', 'CurriculumController@editOwner')->name('curricula.editOwner');
    Route::patch('curricula/{curriculum}/editOwner', 'CurriculumController@storeOwner')->name('curricula.storeOwner');
    Route::get('curricula/{curriculum}/objectives', 'CurriculumController@getObjectives')->name('curricula.getObjectives');
    Route::get('curricula/{curriculum}/print', 'CurriculumController@print')->name('curricula.print');
    Route::patch('curricula/{curriculum}/resetOrderIds', 'CurriculumController@resetOrderIds')->name('curricula.resetOrderIds');
    Route::put('curricula/{curriculum}/syncObjectiveTypesOrder', 'CurriculumController@syncObjectiveTypesOrder')->name('curricula.syncObjectiveTypesOrder');
    Route::get('curricula/{curriculum}/terminalObjectives', 'CurriculumController@getTerminalObjectives')->name('curricula.getTerminalObjectives');
    Route::put('curricula/{curriculum}/variantDefinitions', 'CurriculumController@setVariantDefinitions');
    Route::resource('curricula', 'CurriculumController');
    Route::post('curriculumSubscriptions/expel', 'CurriculumSubscriptionController@expel')->name('curriculumSubscriptions.expel');
    Route::resource('curriculumSubscriptions', 'CurriculumSubscriptionController');
    Route::resource('curriculumTypes', 'CurriculumTypeController');
    // Import/Export
    Route::post('curricula/import/store', 'CurriculumImportController@store')->name('curricula.import.store');
    Route::get('curricula/{curriculum}/export', 'CurriculumExportController@export')->name('curricula.export');
// D
// E
    /*** Enabling-Objectives ***/
    Route::get('enablingObjectives/{enablingObjective}/referenceSubscriptionSiblings', 'EnablingObjectiveController@referenceSubscriptionSiblings');
    Route::get('enablingObjectives/{enablingObjective}/quoteSubscriptions', 'EnablingObjectiveController@quoteSubscriptions');
    Route::get('enablingObjectives/{enablingObjective}/achievements/{group?}', 'EnablingObjectiveController@showAchievements')->name('enablingObjectives.showAchievements');
    Route::patch('enablingObjectives/{enablingObjective}/higher', 'EnablingObjectiveController@higher');
    Route::patch('enablingObjectives/{enablingObjective}/lower', 'EnablingObjectiveController@lower');
    Route::resource('enablingObjectives', 'EnablingObjectiveController');
    Route::post('enablingObjectiveSubscriptions/destroy', 'EnablingObjectiveSubscriptionsController@destroy');
    Route::resource('enablingObjectiveSubscriptions', 'EnablingObjectiveSubscriptionsController');

    /*** Exams ***/
    Route::get('exams/list', 'Tests\ExamController@list');
    Route::delete('exams/{exam}', 'Tests\ExamController@delete')->middleware('can:test_delete');
    Route::get('exams/{exam}/list', 'Tests\ExamController@listExamUsers');
    Route::get('exams/{exam}/users/list', 'Tests\ExamController@listAllUsers');
    Route::post('exams/{exam}/status', 'Tests\ExamController@getExamStatus');
    Route::post('exams/{exam}/users/enrol', 'Tests\ExamController@addUsers');
    Route::delete('exams/{exam}/users/expel', 'Tests\ExamController@removeUsers');
    Route::post('exams/{exam}/report', 'Tests\ExamController@getReport');

    /*** Exercises ***/
    Route::resource('exercises', 'ExerciseController');
    Route::resource('exerciseDones', 'ExerciseDoneController');
// F
// G
    Route::get('grades/list', 'GradesController@list')->name('grades.list');
    Route::delete('grades/destroy', 'GradesController@massDestroy')->name('grades.massDestroy');

    /*** Groups ***/
    Route::post('groups/enrol', 'GroupsController@enrol')->name('groups.enrol');
    Route::delete('groups/expel', 'GroupsController@expel')->name('groups.expel');
    Route::delete('groups/massDestroy', 'GroupsController@massDestroy')->name('groups.massDestroy');
    Route::get('groups/list', 'GroupsController@list');
// H
// I
// J
// K
    /*** Kanbans ***/
    Route::get('kanbans/list', 'KanbanController@list');
    Route::get('kanbans/{kanban}/copy', 'KanbanController@copyKanban');
    Route::get('export_csv/{kanban}', 'KanbanController@exportKanbanCsv');
    Route::get('export_pdf/{kanban}', 'KanbanController@exportKanbanPdf');
    Route::resource('kanbans', 'KanbanController');
    Route::post('kanbanSubscriptions/expel', 'KanbanSubscriptionController@expel');
    Route::resource('kanbanSubscriptions', 'KanbanSubscriptionController');
    // KanbanItems
    Route::post('kanbanItems/{kanbanItem}/react', 'KanbanItemController@reaction')->name('kanbanItems.react');
    Route::get('kanbanItems/{kanbanItem}/editors', 'KanbanItemController@editors')->name('kanbanItems.editors');
    Route::get('kanbanItems/{item}/copy', 'KanbanItemController@copyItem');
    Route::put('kanbanItems/sync', 'KanbanItemController@sync')->name('kanbanItems.sync');
    Route::resource('kanbanItems', 'KanbanItemController');
    Route::resource('kanbanItemSubscriptions', 'KanbanItemSubscriptionController');
    // KanbanStatus
    Route::get('kanbanStatuses/{kanban}/checkSync', 'KanbanStatusController@checkSync');
    Route::get('kanbanStatuses/{status}/copy', 'KanbanStatusController@copyStatus');
    Route::put('kanbanStatuses/sync', 'KanbanStatusController@sync')->name('kanbanStatuses.sync');
    Route::resource('kanbanStatuses', 'KanbanStatusController');
    // Comments
    Route::post('kanbanItemComments/{kanbanItemComment}/react', 'KanbanItemCommentController@reaction')->name('kanbanItemCommentController.react');
    Route::resource('kanbanItemComment', 'KanbanItemCommentController');
// L
    Route::post('lmsReferences/get', 'LmsReferenceController@get')->name('lmsReferences.get');

    /*** Logbooks ***/
    Route::get('logbooks/list', 'LogbookController@list');
    Route::get('logbooks/{logbook}/print', 'LogbookController@print')->name('logbooks.print');
    Route::resource('logbooks', 'LogbookController');
    Route::post('logbookSubscriptions/expel', 'LogbookSubscriptionController@expel')->name('logbookSubscriptions.expel');
    Route::resource('logbookSubscriptions', 'LogbookSubscriptionController');
    // Logbook-Entries
    Route::patch('logbookEntries/{logbookEntry}/setSubject', 'LogbookEntryController@setSubject');
    Route::resource('logbookEntries', 'LogbookEntryController');
// M
    /*** Maps ***/
    Route::get('maps/list', 'MapController@list');
    Route::resource('maps', 'MapController');
    Route::resource('mapSubscriptions', 'MapSubscriptionController');
    Route::resource('mapMarkers', 'MapMarkerController');
    Route::resource('mapMarkerSubscriptions', 'MapMarkerSubscriptionController');
    Route::resource('mapMarkerTypes', 'MapMarkerTypeController');
    Route::resource('mapMarkerCategories', 'MapMarkerCategoryController');

    /*** Media ***/
    Route::get('media/list', 'MediumController@list')->name('media.list');
    Route::post('media/{medium}/destroy', 'MediumController@destroy'); // has to be post (has parameters)
    Route::get('media/{medium}/thumb', 'MediumController@thumb')->name('media.thumb');
    Route::resource('media', 'MediumController');
    Route::post('mediumSubscriptions/destroy', 'MediumSubscriptionController@destroySubscription');
    Route::resource('mediumSubscriptions', 'MediumSubscriptionController');
// N
    /*** Navigators ***/
    Route::get('navigators/list', 'NavigatorController@list');
    Route::get('navigators/{navigator}/list', 'NavigatorController@listViews')->name('navigator.views');
    Route::resource('navigators', 'NavigatorController');
    Route::resource('navigatorItems', 'NavigatorItemController');
    Route::get('navigatorViews/{navigator_view}/list', 'NavigatorViewController@list');
// O
    Route::get('objectiveTypes/list', 'ObjectiveTypeController@list')->name('objectiveTypes.list');

    /*** Organizations ***/
    Route::post('organizations/enrol', 'OrganizationsController@enrol')->name('organizations.enrol');
    Route::delete('organizations/expel', 'OrganizationsController@expel')->name('organizations.expel');
    Route::get('organizations/list', 'OrganizationsController@list')->name('organizations.list');
    // Organization-Types
    Route::get('organizationTypes/list', 'OrganizationTypesController@list')->name('organizationTypes.list');
// P
    Route::get('periods/list', 'PeriodController@list')->name('periods.list');

    Route::get('permissions/list', 'PermissionsController@list')->name('permissions.list');
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');

    /*** Plans ***/
    Route::get('plans/list', 'PlanController@list');
    Route::get('plans/{plan}/copy', 'PlanController@copyPlan');
    Route::get('plans/{plan}/getUsers', 'PlanController@getUsers');
    Route::put('plans/{plan}/syncEntriesOrder', 'PlanController@syncEntriesOrder')->name('plans.syncEntriesOrder');
    Route::get('plans/{plan}/getUserAchievements/{userIds}', 'PlanController@getUserAchievements');
    Route::resource('plans', 'PlanController');
    Route::resource('planTypes', 'PlanTypeController');
    Route::post('planSubscriptions/expel', 'PlanSubscriptionController@expel');
    Route::resource('planSubscriptions', 'PlanSubscriptionController');
    Route::resource('planEntries', 'PlanEntryController');
// Q
    Route::resource('qrCodes', 'QRCodeController');
// R
    /*** Repository ***/
    Route::post('repositorySubscriptions/destroySubscription', 'RepositorySubscriptionController@destroySubscription')->name('repositorySubscriptions.destroySubscription');
    Route::post('repositorySubscriptions/searchRepository', 'RepositorySubscriptionController@searchRepository')->name('repositorySubscriptions.searchRepository');
    Route::get('repositorySubscriptions/getMedia', 'RepositorySubscriptionController@getMedia')->name('repositorySubscriptions.getMedia');
    Route::resource('repositorySubscriptions', 'RepositorySubscriptionController');

    Route::get('roles/list', 'RolesController@list')->name('roles.list');
// S
    /*** Subjects ***/
    Route::get('subjects/list', 'SubjectController@list')->name('subjects.list');
    Route::get('subjects/getSubject', 'SubjectController@getSubject');
// T
    /*** Tasks ***/
    Route::get('tasks/list', 'TaskController@list')->name('tasks.list');
    Route::patch('tasks/{task}/complete', 'TaskController@complete')->name('tasks.complete');
    Route::get('tasks/{task}/activity', 'TaskController@activity')->name('tasks.activity');
    Route::resource('taskSubscriptions', 'TaskSubscriptionController');

    /*** Terminal-Objectives ***/
    Route::get('terminalObjectives/{terminalObjective}/enablingObjectives', 'TerminalObjectiveController@getEnablingObjectives');
    Route::get('terminalObjectives/{terminalObjective}/referenceSubscriptionSiblings', 'TerminalObjectiveController@referenceSubscriptionSiblings');
    Route::get('terminalObjectives/{terminalObjective}/quoteSubscriptions', 'TerminalObjectiveController@quoteSubscriptions');
    Route::patch('terminalObjectives/{terminalObjective}/higher', 'TerminalObjectiveController@higher');
    Route::patch('terminalObjectives/{terminalObjective}/lower', 'TerminalObjectiveController@lower');
    Route::resource('terminalObjectives', 'TerminalObjectiveController');
    Route::post('terminalObjectiveSubscriptions/destroy', 'TerminalObjectiveSubscriptionsController@destroySubscription');
    Route::resource('terminalObjectiveSubscriptions', 'TerminalObjectiveSubscriptionsController');

    /*** Trainings ***/
    Route::resource('trainings', 'TrainingController');
    Route::patch('trainingsSubscriptions/{trainingSubscription}/lower', 'TrainingSubscriptionController@lower');
    Route::patch('trainingsSubscriptions/{trainingSubscription}/higher', 'TrainingSubscriptionController@higher');
    Route::resource('trainingSubscriptions', 'TrainingSubscriptionController');

    Route::post('tokens', 'ShareTokenController@create');
// U
    /*** Users ***/
    Route::get('users/list', 'UsersController@list');
    Route::patch('users/setCurrentOrganization', 'UsersController@setCurrentOrganization')->name('users.setCurrentOrganization');
    Route::patch('users/setCurrentPeriod', 'UsersController@setCurrentPeriod')->name('users.setCurrentPeriod');
    Route::patch('users/setAvatar', 'UsersController@setAvatar')->name('users.setAvatar');
    Route::get('users/{user}/dsgvoExport', 'UsersController@dsgvoExport')->name('users.dsgvoExport');
    Route::get('users/{user}/avatar', 'UsersController@getAvatar');
    Route::resource('users', 'UsersController');
// V
    Route::get('variantDefinitions/list', 'VariantDefinitionController@list');

    /*** Videoconferences ***/
    Route::get('videoconferences/list', 'VideoconferenceController@list');
    Route::get('videoconferences/servers', 'VideoconferenceController@servers');
    Route::get('videoconferences/{videoconference}/getStatus', 'VideoconferenceController@getStatus');
    Route::get('videoconferences/{videoconference}/start', 'VideoconferenceController@start');
    Route::resource('videoconferences', 'VideoconferenceController');
    Route::post('videoconferenceSubscriptions/expel', 'VideoconferenceSubscriptionController@expel');
    Route::resource('videoconferenceSubscriptions', 'VideoconferenceSubscriptionController');
});

// only authenticate requests that return a blade-file (initial requests) to avoid reduntant authentication
// keep the resource-routes in here, unless the index/show routes are specifically defined
Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/admin', 'AdminController@index')->name('admin.index');

    Route::resource('calendarEvents', 'CalendarEventController');

    Route::resource('agendas', 'AgendaController');
    Route::resource('agendaItems', 'AgendaItemController');
    Route::resource('agendaItemTypes', 'AgendaItemTypeController');
    Route::resource('agendaItemSubscriptions', 'AgendaItemSubscriptionController');
    Route::resource('agendaItemSpeakers', 'AgendaItemSpeakerController');

    Route::resource('absences', 'AbsenceController');

    Route::resource('artefacts', 'ArtefactController');

    Route::resource('categories', 'CategorieController');

    Route::resource('certificates', 'CertificateController');

    Route::resource('configs', 'ConfigController');

    Route::resource('contactDetails', 'ContactDetailController');

    Route::resource('contents', 'ContentController');

    Route::resource('contentSubscriptions', 'ContentSubscriptionController');

    Route::get('courses/{course}', 'CourseController@show');

    Route::resource('countries', 'CountryController');

    Route::get('curricula', 'CurriculumController@index')->name('curricula.index');
    Route::get('curricula/{curriculum}/token', 'CurriculumController@getCurriculumByToken');

    Route::get('enablingObjectives/{enablingObjective}', 'EnablingObjectiveController@show');

    /* plugin eventmanagment */
    Route::post('eventSubscriptions/destroySubscription', 'EventSubscriptionController@destroySubscription')->name('eventSubscriptions.destroySubscription');
    Route::post('eventSubscriptions/search', 'EventSubscriptionController@search')->name('eventSubscriptions.search');
    Route::post('eventSubscriptions/getEvents', 'EventSubscriptionController@getEvents')->name('eventSubscriptions.getEvents');

    Route::resource('eventSubscriptions', 'EventSubscriptionController');

    /* Exams */
    Route::get('exams_subscribed', 'Tests\ExamController@authUserIndexExams')->name('exams.index');
    Route::get('exams', 'Tests\ExamController@index');
    Route::post('exams', 'Tests\ExamController@create');
    Route::get('exams/{exam}/edit', 'Tests\ExamController@show');

    Route::resource('glossar', 'GlossarController');

    Route::resource('grades', 'GradesController');

    Route::resource('groups', 'GroupsController');

    Route::get('kanbans', 'KanbanController@index')->name('kanbans.index');
    Route::get('kanbans/{kanban}', 'KanbanController@show');
    Route::get('kanbans/{kanban}/token', 'KanbanController@getKanbanByToken');

    Route::resource('levels', 'LevelController');

    Route::resource('lmsReferences', 'LmsReferenceController');
    Route::resource('lmsReferenceSubscriptions', 'LmsReferenceSubscriptionController');
    Route::resource('lmsUserTokens', 'LmsUserTokenController');

    Route::get('logbooks', 'LogbookController@index')->name('logbooks.index');
    Route::get('logbooks/{logbook}', 'LogbookController@show');

    Route::get('maps', 'MapController@index')->name('maps.index');
    Route::get('maps/{map}', 'MapController@show');
    Route::get('maps/{map}/token', 'MapController@getMapByToken');

    Route::get('media', 'MediumController@index')->name('media.index');

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
    Route::get('navigators', 'NavigatorController@index')->name('navigators.index');
    Route::get('navigators/{navigator}', 'NavigatorController@show');
    Route::resource('navigatorViews', 'NavigatorViewController');

    /* Notes */
    Route::resource('notes', 'NoteController');

    Route::get('meetings/list', 'MeetingController@list')->name('meetings.list');
    Route::get('meetings/getImportDataByUid', 'MeetingController@getImportDataByUid')->name('meetings.getImportDataByUid');
    Route::resource('meetings', 'MeetingController');

    Route::resource('meetingDates', 'MeetingDateController');

    /* objectiveTypes */
    Route::resource('objectiveTypes', 'ObjectiveTypeController');

    Route::resource('organizations', 'OrganizationsController');

    /* organizationtype */
    Route::resource('organizationTypes', 'OrganizationTypesController');

    /* period */
    Route::resource('periods', 'PeriodController');

    Route::resource('permissions', 'PermissionsController');

    Route::get('plans', 'PlanController@index')->name('plans.index');
    Route::get('plans/{plan}', 'PlanController@show');

    Route::resource('prerequisites', 'PrerequisitesController');

    Route::get('print/content/{content}', 'PrintController@content')->name('print.content');
    Route::get('print/glossar/{glossar}', 'PrintController@glossar')->name('print.glossar');
    Route::get('print/{model}/{id}', 'PrintController@model')->name('print.model');
    /*Route::get('print/curriculum/{curriculum}', 'PrintController@curriculum')->name('print.curriculum');*/
    Route::get('print/curriculum/{curriculum}/references', 'PrintController@references')->name('print.references');

    Route::resource('progresses', 'ProgressController');

    /* reference(Subscription)  */
    Route::resource('references', 'ReferenceController');
    Route::resource('referenceSubscriptions', 'ReferenceSubscriptionController');

    Route::resource('roles', 'RolesController');

    Route::resource('sharingLevels', 'SharingLevelController');

    Route::resource('statistics', 'StatisticController');

    Route::resource('statusdefinitions', 'StatusDefinitionController');

    Route::resource('subjects', 'SubjectController');

    Route::resource('tasks', 'TaskController');

    Route::get('terminalObjectives/{terminalObjective}', 'TerminalObjectiveController@show');

    Route::get('trainings/{training}', 'TrainingController@show');

    /* Tests */
    Route::get('tests', 'Tests\TestController@index');

    /* User */
    Route::delete('users/massDestroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::patch('users/massUpdate', 'UsersController@massUpdate')->name('users.massUpdate');
    Route::get('users/import', 'UsersController@createImport')->name('users.createImport');
    Route::post('users/import', 'UsersController@storeImport')->name('users.storeImport');
    Route::delete('users/{user}/forceDestroy', 'UsersController@forceDestroy')->name('users.forceDestroy');
    Route::get('users', 'UsersController@index')->name('users.index');
    Route::get('users/{user}', 'UsersController@show')->name('users.show');

    Route::resource('variants', 'VariantController');
    Route::resource('variantDefinitions', 'VariantDefinitionController');

    Route::get('videoconferences', 'VideoconferenceController@index')->name('videoconferences.index');
    Route::get('videoconferences/{videoconference}', 'VideoconferenceController@show');
    Route::get('videoconferences/{videoconference}/token', 'VideoconferenceController@getVideoconferenceByToken');
    Route::get('videoconferences/{videoconference}/startWithPw', 'VideoconferenceController@show');
});


if (env('GUEST_USER') !== null) {
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
