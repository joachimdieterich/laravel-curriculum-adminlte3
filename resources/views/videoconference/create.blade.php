@extends('layouts.master')
@section('title')
    {{ trans('global.videoconference.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item "><a href="/videoconferences">{{ trans('global.videoconference.title') }}</a></li>
    <li class="breadcrumb-item active"> {{ trans('global.videoconference.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <videoconference
            :create="true"></videoconference>
    </div>

</div>
@can('medium_create')
    <medium-create-modal></medium-create-modal>
@endcan
@can('videoconference_create')
    <subscribe-modal></subscribe-modal>
@endcan
@endsection
