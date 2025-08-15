@extends('layouts.master')

@section('title')
    <title-component></title-component>
@endsection

@section('breadcrumb')
    @if (Auth::user()->id == env('GUEST_USER'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> Str::limit($meeting->title, 10) ]
        ])}}"
        ></breadcrumbs>
    @else
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.meeting.title_singular'), 'url' => "/meetings"],
            ['active'=> true, 'title'=> Str::limit($meeting->title, 10) ]
        ])}}"
        ></breadcrumbs>
    @endif
@endsection
@section('content')
    <meeting :meeting="{{ $meeting }}" ref="meetings"></meeting>
@endsection
