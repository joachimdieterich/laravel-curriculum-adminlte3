@extends('layouts.master')
@section('title')
    <title-component></title-component>
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> $group->title ]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <group :group="{{ $group }}"></group>
@endsection