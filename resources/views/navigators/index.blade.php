@extends('layouts.master')
@section('title')
    {{ trans('global.navigator.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.navigator.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <navigators></navigators>
@endsection
