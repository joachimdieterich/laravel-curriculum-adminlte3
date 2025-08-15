@extends('layouts.master')
@section('title')
    {{ trans('global.role.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.role.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <roles></roles>
@endsection
