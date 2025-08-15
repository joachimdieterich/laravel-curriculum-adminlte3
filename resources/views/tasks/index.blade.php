@extends('layouts.master')
@section('title')
    {{ trans('global.task.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.task.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <tasks></tasks>
@endsection
