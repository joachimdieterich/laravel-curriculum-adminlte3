@extends('layouts.master')
@section('title')
    {{ trans('global.role.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.role.title_singular'), 'url' => "/roles"],
            ['active'=> true, 'title'=> $role->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <role
        :role="{{ $role }}">
    </role>
@endsection
