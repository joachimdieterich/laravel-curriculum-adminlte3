@extends('layouts.master')
@section('title')
    {{ trans('global.videoconference.title_singular') }}
    @can('videoconference_create')
        @if($videoconference->owner_id == auth()->user()->id)
            <button class="btn btn-flat"
                    onclick="app.__vue__.$modal.show('subscribe-modal',  {'modelId': {{ $videoconference->id }}, 'modelUrl': 'videoconference','shareWithToken': true });">
                <i class="fa fa-share-alt text-secondary"></i>
            </button>
        @endif
    @endcan
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item "><a href="/videoconferences">{{ trans('global.videoconference.title') }}</a></li>
    <li class="breadcrumb-item active"> {{ $videoconference->meetingName }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <videoconference
            :videoconference="{{ $videoconference }}"
            editor="true"
        ></videoconference>
    </div>

</div>
@can('medium_create')
    <medium-create-modal></medium-create-modal>
@endcan
@can('videoconference_create')
    <subscribe-modal></subscribe-modal>
@endcan
@endsection
