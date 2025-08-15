@extends('layouts.master')
@section('title')
    {{ trans('global.grade.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.grade.title_singular'), 'url' => "/grades"],
            ['active'=> true, 'title'=> $grade->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <grade
        :grade="{{ $grade }}">
    </grade>
@endsection
