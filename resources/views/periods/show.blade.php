@extends('layouts.master')
@section('title')
    {{ trans('global.period.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.period.title_singular'), 'url' => "/periods"],
            ['active'=> true, 'title'=> $period->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <period
        :period="{{ $period }}"
    ></period>
@endsection
