@extends('layouts.master')
@section('title')
    {{ trans('global.objectiveType.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.objectiveType.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <objective-types></objective-types>
@endsection
