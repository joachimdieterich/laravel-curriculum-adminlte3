@extends('layouts.master')
@section('title')
    {{ trans('global.config.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.config.title'), 'url' => "/configs"],
            ['active'=> true, 'title'=> $config->key]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <config
        :config="{{ $config }}"
    ></config>
@endsection
