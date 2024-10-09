@extends('layouts.master')
@section('title')
    {{ trans('global.meeting.title') }}
@endsection
@section('breadcrumb')
    @if (Auth::user()->id == env('GUEST_USER'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.meeting.title_singular'), 'url' => "/meetings/" . Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id],
        ])}}"
        ></breadcrumbs>
    @else
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.meeting.title')],
            ])}}"
        ></breadcrumbs>
    @endif
@endsection
@section('content')
<meetings></meetings>

@endsection
