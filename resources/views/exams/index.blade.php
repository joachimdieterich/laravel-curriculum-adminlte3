@extends('layouts.master')
@section('title')
    {{ trans('global.exam.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.exam.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')

<exams></exams>

@include('exams.examlist', [
        "exams" =>  $exams,
    ])

<!--
<task-modal></task-modal>
-->

@endsection
