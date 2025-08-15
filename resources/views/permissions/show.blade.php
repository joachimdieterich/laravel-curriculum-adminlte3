@extends('layouts.master')
@section('title')
    {{ trans('global.permission.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.permission.title_singular'), 'url' => "/grades"],
            ['active'=> true, 'title'=> $permission->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <permission
        :permission="{{ $permission }}">
    </permission>
@endsection
