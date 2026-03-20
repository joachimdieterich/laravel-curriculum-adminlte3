@extends('layouts.master')

@section('title')
    {{ trans('global.kanban.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{ json_encode([
            ['active' => true, 'title' => trans('global.kanban.title')],
        ]) }}"
    ></breadcrumbs>
@endsection
@section('content')
    <kanbans model-url="kanbans"></kanbans>
@endsection