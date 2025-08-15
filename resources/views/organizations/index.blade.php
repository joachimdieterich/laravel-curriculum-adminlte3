@extends('layouts.master')
@section('title')
    {{ trans('global.organization.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.organization.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <organizations></Organizations>
@endsection
