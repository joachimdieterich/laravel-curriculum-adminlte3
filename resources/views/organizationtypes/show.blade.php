@extends('layouts.master')
@section('title')
    {{ trans('global.organizationType.title_singular') }}
@endsection
@section('content')
    <organization-type :organization-type="{{ $organizationType }}"></organization-type>
@endsection