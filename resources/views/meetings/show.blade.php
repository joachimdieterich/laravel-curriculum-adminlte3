@extends('layouts.master')

@section('title')
    {{ trans('global.meeting.title_singular') }}
    <a class="text-secondary text-lg" href="/meetings/{{$meeting->id}}/edit">
        <i class="fa fa-pencil-alt"></i>
    </a>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.meeting.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    <meeting :meeting="{{ $meeting }}" ref="meetings"></meeting>
    <medium-modal></medium-modal>
    <medium-create-modal></medium-create-modal>
@endsection
