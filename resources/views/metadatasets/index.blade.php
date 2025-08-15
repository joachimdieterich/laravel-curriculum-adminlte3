@extends('layouts.master')
@section('title')
    {{ trans('global.metadataset.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.metadataset.title'), 'url' => "/kanbans"],
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <metadatasets></metadatasets>
@endsection
