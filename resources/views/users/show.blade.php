@extends('layouts.master')
@section('title')
    {{ trans('global.myProfile') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> false, 'title'=> trans('global.user.title'), 'url' => "/users"],
            ['active'=> true, 'title'=> trans('global.myProfile')],
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <user :user="{{ $user }}"></user>
@can('is_admin')
<div class="row">
    <div class="col-12">
        @if (auth()->user()->role()->id == 1)
        <div class="card">
            <div class="card-header p-2">Debug</div>
            <div class="card-body">
                User
                <li class="small">id: {{ $user->id }};</li>
                <li class="small">common_name: {{ $user->common_name }};</li>
                <li class="small">firstname: {{ $user->firstname }};</li>
                <li class="small">lastname: {{ $user->lastname }};</li>
                <li class="small">email: {{ $user->email }};</li>
                <li class="small">created_at: {{ $user->created_at }};</li>
                <li class="small">status: {{ $user->status }};</li>
                <li class="small">current_organization_id: {{ $user->current_organization_id }} => {{ $user->organizations->find($user->current_organization_id)?->title }};</li>
                <li class="small">current_period_id: {{ $user->current_period_id }};</li>

                <br/>

                currentCurriculaEnrolments:
                @foreach(App\User::find($user->id)->currentCurriculaEnrolments() as $cur_enr)
                    <li class="small">id: {{ $cur_enr->id }} => {{ $cur_enr->title }};  course_id: {{ $cur_enr->course_id }};  group_id: {{ $cur_enr->group_id }};</li>
                @endforeach

                <br/>

                currentGroupEnrolments:
                @foreach(App\User::find($user->id)->currentGroupEnrolments as $grp_enr)
                    <li class="small">id: {{ $grp_enr->id }} => {{ $grp_enr->title }};  period_id: {{ $grp_enr->period_id }};  course_id: {{ $grp_enr->course_id }};</li>
                @endforeach

                <br/>

                Groups:
                @foreach(App\User::find($user->id)->groups as $groups)
                    <li class="small">id: {{ $groups->id }} => {{ $groups->title }};  period_id: {{ $groups->period_id }};  organization_id: {{ $groups->organization_id }};</li>
                @endforeach

                <br/>

                currentCurriculaPeriods:
                @foreach(App\User::find($user->id)->currentPeriods() as $cur_period)
                    <li class="small">id: {{ $cur_period->id }} => {{ $cur_period->title }}; organization_id: {{ $cur_period->organization_id }};</li>
                @endforeach

                <br/>

                organizations:
                @foreach(App\User::find($user->id)->organizations as $org)
                    <li class="small">id: {{ $org->id }} => {{ $org->title }};</li>
                @endforeach
                <br/>
            </div>
        </div>
        @endif
    </div>
</div>
@endcan

@endsection

@section('scripts')
@parent
<script>

function setAvatar()
{
    $.ajax({
        headers: {'x-csrf-token': _token},
            method: 'POST',
            url: "{{ route('users.setAvatar') }}",
            data: {
                medium_id: $('#medium_id').val(),
                _method: 'PATCH',
            }
    })
    .done(function () { location.reload() })
}
</script>

@endsection