@extends('layouts.master')
@section('title')
    {{ trans('global.group.title') }}
@endsection
@section('content')
    <Groups :organizations="{{ $organizations }}"/>
@endsection