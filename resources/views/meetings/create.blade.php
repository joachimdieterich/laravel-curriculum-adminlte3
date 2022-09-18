@extends('layouts.master')
@section('title')
    {{ trans('global.meeting.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.meeting.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"
                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("meetings.store") }}"
                  method="POST"
                  enctype="multipart/form-data">
                @include ('meetings.form', [
                    'meeting' => new App\Meeting,
                    'buttonText' => trans('global.meeting.create')
                ])
            </form>
    </div>
</div>
@endsection
