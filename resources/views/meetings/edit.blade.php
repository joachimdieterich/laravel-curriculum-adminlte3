@extends('layouts.master')
@section('title')
    {{ trans('global.meeting.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.meeting.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"
                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')


    <div class="card">
        <div class="card-body">
            <form action="{{ route("meetings.update", [$meeting->id]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @include('meetings.form', [
                    'meeting'     => $meeting,
                    'buttonText'    => trans('global.meeting.edit')
                ])
            </form>
        </div>
    </div>

@endsection
