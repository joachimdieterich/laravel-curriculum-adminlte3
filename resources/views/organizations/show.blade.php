@extends('layouts.master')
@section('title')
    {{ trans('global.organization.title_singular') }}
@endsection
@section('content')
    <organization
        :organization="{{ $organization }}"
        :status_definitions="{{ $status_definitions }}"
    ></organization>
@endsection