@extends('layouts.master')
@section('title')
    {{ trans('global.organization.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item "><a href="/organizations">{{ trans('global.organization.title_singular') }}</a></li>
    <li class="breadcrumb-item active">{{ $organization->title }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    <organization
        :organization="{{ $organization }}"
        :status_definitions="{{ $status_definitions }}"
    ></organization>
@endsection
