@extends('layouts.master')
@section('title')
    {{ trans('global.objectiveType.title_singular') }}
@endsection
@section('content')
    <Objective-Type :objective-type="{{ $objectiveType }}"/>
@endsection