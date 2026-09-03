@extends('layouts.master')
@section('title')
    {{ trans('global.map.title') }}
@endsection
@section('content')
    <maps model-url="maps"></maps>
@endsection
