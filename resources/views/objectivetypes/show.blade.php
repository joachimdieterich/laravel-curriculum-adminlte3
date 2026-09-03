@extends('layouts.master')
@section('title')
    {{ trans('global.objectiveType.title_singular') }}
@endsection
@section('content')
    <objective-type :objective-type="{{ $objectiveType }}"></objective-type>
@endsection