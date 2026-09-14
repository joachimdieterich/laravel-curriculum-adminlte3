@extends('layouts.master')
@section('title')
    {{ trans('global.media.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.medium.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <media-index/>
@endsection