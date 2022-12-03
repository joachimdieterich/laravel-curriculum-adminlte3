@extends('layouts.master')
@section('title')
    {{ trans('global.exam.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.exam.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@include('exams.examlist', [
        "exams" =>  $exams,
    ])

<task-modal></task-modal>
@endsection
