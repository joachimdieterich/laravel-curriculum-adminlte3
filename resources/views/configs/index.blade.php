@extends('layouts.master')
@section('title')
    {{ trans('global.config.title') }}

@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.config.title')],
            ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <configs></configs>
@endsection

