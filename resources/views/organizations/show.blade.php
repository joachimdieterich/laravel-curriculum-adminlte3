@extends('layouts.master')
@section('title')
    {{ trans('global.organization.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.organization.title_singular'), 'url' => "/organizations"],
            ['active'=> true, 'title'=> $organization->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <organization
        :organization="{{ $organization }}"
        :status_definitions="{{ $status_definitions }}"
    ></organization>
@endsection
