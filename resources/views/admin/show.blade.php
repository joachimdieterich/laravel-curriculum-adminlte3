@extends('layouts.master')
@section('title')
    Administration
@endsection
@section('content')
    <Admin-View></Admin-View>
    <div class="card px-3">
        <div class="p-2">{{ trans('global.exam.title') }}</div>
        <div class="card-footer bg-light p-0">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <span class="nav-link">
                        {{ trans('global.exam.title') }}
                        <span class="float-end">{{ $exam['all'] }}</span>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link">
                        {{ trans('global.exam.status_options.started') }}
                        <span class="float-end">{{ $exam['started'] }}</span>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link">
                        {{ trans('global.exam.status_options.completed') }}
                        <span class="float-end">{{ $exam['completed'] }}</span>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link">
                        {{ trans('global.organization.title') }}
                        <span class="float-end">{{ $exam['organizations'] }}</span>
                    </span>
                </li>
            </ul>
        </div>
    </div>
@endsection