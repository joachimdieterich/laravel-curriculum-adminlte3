@extends('layouts.master')
@section('title')
    {{ trans('global.tag.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.tag.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <tags></tags>
@endsection
