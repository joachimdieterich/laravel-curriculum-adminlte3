@extends('layouts.master')
@section('title')
    {{ trans('global.objectiveType.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.objectiveType.title_singular'), 'url' => "/periods"],
            ['active'=> true, 'title'=> $objectiveType->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <objective-type
        :objective-type="{{ $objectiveType }}"></objective-type>
@endsection
