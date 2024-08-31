@extends('layouts.master')
@section('title')
    {{ trans('global.organizationtype.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.organizationType.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <organization-types></organization-types>
@endsection
