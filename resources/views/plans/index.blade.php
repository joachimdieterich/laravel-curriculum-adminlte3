@extends('layouts.master')
@section('title')
    {{ trans('global.plan.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.plan.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <plans></plans>
@endsection
