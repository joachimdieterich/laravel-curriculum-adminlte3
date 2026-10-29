@extends('layouts.master')
@section('title')
    {{ trans('global.organizationType.title_singular') }}
@endsection
@section('content')
    <Organization-Type :organization-type="{{ $organizationType }}"/>
@endsection