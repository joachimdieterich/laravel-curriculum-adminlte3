@extends('layouts.master')
@section('title')
    {{ trans('global.organizationType.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.organizationType.title'), 'url' => "/organizationTypes"],
            ['active'=> true, 'title'=> $organizationType->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <organization-type
        :organizationType="{{ $organizationType }}"
    ></organization-type>
@endsection
