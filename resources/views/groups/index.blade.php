@extends('layouts.master')
@section('title')
    {{ trans('global.group.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.group.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
   <groups></groups>
@endsection
