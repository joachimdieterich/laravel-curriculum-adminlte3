@extends('layouts.master')
@section('title')
    {{ trans('global.myProfile') }}
@endsection
@section('content')
    <User :user="{{ $user }}"></User>

    @can('is_admin')
    <div class="d-flex flex-column mx-3 mb-3 bg-white rounded-3 shadow-layout">
        <h3 class="p-2 border-bottom border-dark-subtle">Debug</h3>

        <div class="px-2">
            <strong>User Details</strong>
            <ul class="small">
                <li>id: {{ $user->id }}</li>
                <li>common_name: {{ $user->common_name }}</li>
                <li>firstname: {{ $user->firstname }}</li>
                <li>lastname: {{ $user->lastname }}</li>
                <li>email: {{ $user->email }}</li>
                <li>created_at: {{ $user->created_at }}</li>
                <li>status: {{ $user->status }}</li>
                <li>current_organization_id: {{ $user->current_organization_id }} => {{ $user->organizations->find($user->current_organization_id)?->title }}</li>
                <li>current_period_id: {{ $user->current_period_id }}</li>
            </ul>

            <strong>Current Curricula Enrollments</strong>
            <ul class="small">
                @foreach(App\User::find($user->id)->currentCurriculaEnrollments() as $cur_enr)
                    <li>id: {{ $cur_enr->id }} => {{ $cur_enr->title }} (course_id: {{ $cur_enr->course_id }} | group_id: {{ $cur_enr->group_id }})</li>
                @endforeach
            </ul>

            <strong>Current Group Enrollments</strong>
            <ul class="small">
                @foreach(App\User::find($user->id)->currentGroupEnrolments as $grp_enr)
                    <li>id: {{ $grp_enr->id }} => {{ $grp_enr->title }} (period_id: {{ $grp_enr->period_id }} | course_id: {{ $grp_enr->course_id }})</li>
                @endforeach
            </ul>

            <strong>Groups</strong>
            <ul class="small">
                @foreach(App\User::find($user->id)->groups as $groups)
                    <li>id: {{ $groups->id }} => {{ $groups->title }} (period_id: {{ $groups->period_id }} | organization_id: {{ $groups->organization_id }})</li>
                @endforeach
            </ul>

            <strong>Current Curricula Periods</strong>
            <ul class="small">
                @foreach(App\User::find($user->id)->currentPeriods() as $cur_period)
                    <li>id: {{ $cur_period->id }} => {{ $cur_period->title }}; organization_id: {{ $cur_period->organization_id }}</li>
                @endforeach
            </ul>

            <strong>Organizations</strong>
            <ul class="small">
                @foreach(App\User::find($user->id)->organizations as $org)
                    <li>id: {{ $org->id }} => {{ $org->title }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endcan
@endsection