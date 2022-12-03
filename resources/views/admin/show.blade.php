@extends('layouts.master')
@section('title')
    Administration
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">Administration</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"
                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    <div class="row">

        <div class="col-12">
            <admin-view ></admin-view>
            <br>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-light p-0"
                     style="max-height:225px; overflow-y: auto">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                        <span  class="nav-link">
                                {{ trans('global.exam.title') }}
                            <span class="float-right">
                            {{  $exam['all'] }}</span>
                        </span>
                        </li>
                        <li class="nav-item">
                        <span  class="nav-link">
                                {{ trans('global.exam.status_options.started') }}
                            <span class="float-right">
                            {{  $exam['started'] }}</span>
                        </span>
                        </li>
                        <li class="nav-item">
                        <span  class="nav-link">
                            {{ trans('global.exam.status_options.completed') }}
                            <span class="float-right">
                            {{  $exam['completed'] }}</span>
                        </span>
                        </li>
                        <li class="nav-item">
                        <span  class="nav-link">
                            {{ trans('global.organization.title') }}
                            <span class="float-right">
                            {{  $exam['organizations'] }}</span>
                        </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
