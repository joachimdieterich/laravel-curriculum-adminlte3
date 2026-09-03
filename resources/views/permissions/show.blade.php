@extends('layouts.master')
@section('title')
    {{ trans('global.permission.title_singular') }}
@endsection
@section('content')
    <permission :permission="{{ $permission }}"></permission>
@endsection