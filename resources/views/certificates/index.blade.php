@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.certificate.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <certificates></certificates>
@endsection
