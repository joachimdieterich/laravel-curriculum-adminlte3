@extends('layouts.contentonly')

@section('title')
    {{--{{ trans('global.videoconference.title') }}--}}
@endsection
@section('breadcrumb')
<!--    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.videoconference.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>-->
@endsection
@section('content')
    <videoconferences
        model-url="videoconferences"
    >
    </videoconferences>
@can('videoconference_create')
    <subscribe-modal></subscribe-modal>
@endcan

@endsection

