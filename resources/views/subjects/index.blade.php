@extends('layouts.master')
@section('title')
    {{ trans('global.subject.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.subject.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <subjects></subjects>
@endsection
