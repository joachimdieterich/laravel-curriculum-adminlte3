@extends('layouts.master')

@section('title')
    <title-component/>
@endsection

@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.plan.title'), 'url' => "/plans"],
            ['active'=> true, 'title'=> $training->title]
        ])}}"
    ></breadcrumbs>
@endsection

@section('content')
    <Training :training="{{ $training }}"/>
@endsection