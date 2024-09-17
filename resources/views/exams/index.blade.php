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
@endsection
