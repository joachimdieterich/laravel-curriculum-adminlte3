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
<div class="row">
    <div class="col-sm-12">
        <div class="card card-primary">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fas fa-chalkboard-teacher mr-1"></i> {{ $videoconference->meetingName }}
                    </h5>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <videoconference
                    :videoconference="{{ $videoconference }}"
                    :user="{{auth()->user()}}"
                ></videoconference>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="float-left"></div>
                <small class="float-right">
                    {{ $videoconference->updated_at }}
                </small>
            </div>
        </div>
    </div>

</div>
@endsection
