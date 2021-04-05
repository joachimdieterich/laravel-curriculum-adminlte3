@extends('layouts.master')
@section('title')
    {{ trans('global.home') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active"><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="content">
    <div class="row">
        @can('curriculum_access')
            @include('partials.infobox', [
                "infoBoxId" =>  'curriculaBox',
                "infoBoxRoute" =>  route("curricula.index"),
                "infoBoxClass" =>  'info-box-icon bg-info elevation-1',
                "infoBoxIcon" =>  'fas fa-th',
                "infoText" =>  "Curricula",
                "infoBoxNumber" =>  count(auth()->user()->curricula()).'/'.App\Curriculum::all()->count(),
            ])
        @endcan

        @can('logbook_access')
            @include('partials.infobox', [
                "infoBoxId" =>  'logbooksBox',
                "infoBoxRoute" =>  route("logbooks.index"),
                "infoBoxClass" =>  'info-box-icon bg-danger elevation-1',
                "infoBoxIcon" =>  'fas fa-book',
                "infoText" =>  trans('global.logbook.title'),
                "infoBoxNumber" =>  count(auth()->user()->logbookSubscription),
            ])
        @endcan
        @can('task_access')
          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>
            @include('partials.infobox', [
                "infoBoxId" =>  'tasksBox',
                "infoBoxRoute" =>  route("tasks.index"),
                "infoBoxClass" =>  'info-box-icon bg-success elevation-1',
                "infoBoxIcon" =>  'fa fa-tasks',
                "infoText" =>  trans('global.task.title'),
                "infoBoxNumber" =>  App\Task::all()->count(),
            ])
          @endcan
          @can('organization_access')
            @include('partials.infobox', [
                "infoBoxId" =>  'organizationsBox',
                "infoBoxRoute" =>  route("organizations.index"),
                "infoBoxClass" =>  'info-box-icon bg-warning elevation-1',
                "infoBoxIcon" =>  'fas fa-university',
                "infoText" =>  trans('global.organization.title'),
                "infoBoxNumber" =>  count(auth()->user()->organizations).'/'.App\Organization::all()->count(),
                "include" =>  'home.organizationsInfo',
            ])
          @endcan
          @can('group_access')
            @include('partials.infobox', [
                "infoBoxId" =>  'groupsBox',
                "infoBoxRoute" =>  route("groups.index"),
                "infoBoxClass" =>  'info-box-icon bg-purple elevation-1',
                "infoBoxIcon" =>  'fas fa-users',
                "infoText" =>  trans('global.group.title'),
                "infoBoxNumber" =>  count(auth()->user()->groups).'/'.App\Group::all()->count(),
                "include" =>  'home.groupsInfo',
            ])
          @endcan
          @if(auth()->user()->currentRole()->first()->id == 1)
            @include('partials.infobox', [
                "infoBoxId" =>  'usersBox',
                "infoBoxRoute" =>  route("users.index"),
                "infoBoxClass" =>  'info-box-icon bg-primary elevation-1',
                "infoBoxIcon" =>  'fas fa-user',
                "infoText" =>  trans('global.user.title'),
                "infoBoxNumber" =>  count(App\Organization::where('id', auth()->user()->current_organization_id)->get()->first()->users()->get()).'/'.App\User::all()->count(),
            ])
          @endif
          @can('navigator_access')
            @include('partials.infobox', [
                "infoBoxId" =>  'navigatorsBox',
                "infoBoxRoute" =>  route("navigators.index"),
                "infoBoxClass" =>  'info-box-icon bg-olive elevation-1',
                "infoBoxIcon" =>  'fa fa-map-signs',
                "infoText" =>  trans('global.navigator.title'),
                "infoBoxNumber" =>  App\Navigator::all()->count(),
            ])
          @endcan
          @can('period_access')
            @include('partials.infobox', [
                "infoBoxId" =>  'periodsBox',
                "infoBoxRoute" =>  route("periods.index"),
                "infoBoxClass" =>  'info-box-icon bg-pink elevation-1',
                "infoBoxIcon" =>  'fa fa-history',
                "infoText" =>  trans('global.period.title'),
                "infoBoxNumber" =>  App\Period::all()->count(),
            ])
          @endcan


        @include('partials.infobox', [
            "infoBoxId" =>  'archivementsBox',
            "infoBoxRoute" =>  '',
            "infoBoxClass" =>  'info-box-icon bg-pink elevation-1',
            "infoBoxIcon" =>  'fas fa-chart-bar',
            "infoText" =>   trans('global.dashboard.statistic_archievements') ,
            "infoBoxNumber" =>  trans('global.dashboard.today').': '.count(auth()->user()->achievements_today()->where('status', '>=', 10)->where('status', '<', 30))
            .'<br>'.trans('global.dashboard.statistic_archievements_total').': '.count(auth()->user()->achievements->where('status', '>=', 10)->where('status', '<', 30)),
        ])

        @include('partials.infobox', [
            "infoBoxId" =>  'onlineBox',
            "infoBoxRoute" =>  '',
            "infoBoxClass" =>  'info-box-icon bg-pink elevation-1',
            "infoBoxIcon" =>  'fas fa-plug',
            "infoText" =>   trans('global.dashboard.online') ,
            "infoBoxNumber" =>  trans('global.dashboard.now_online').': '. now_online()
            .'<br>'.trans('global.dashboard.today').': '.today_online(),
        ])


    </div>
</div>

@endsection
@section('scripts')
@parent

@endsection
