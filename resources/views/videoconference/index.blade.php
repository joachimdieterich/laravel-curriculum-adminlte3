@extends('layouts.master')

@section('title')
    {{ trans('global.videoconference.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.videoconference.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('videoconference_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-videoconference"
                class="btn btn-success" href="{{ route("videoconferences.create") }}">
                {{ trans('global.videoconference.create') }}
            </a>
        </div>
    </div>
@endcan

<videoconferences model-url="videoconferences"></videoconferences>

@can('videoconference_create')
    <subscribe-modal></subscribe-modal>
@endcan

@endsection

