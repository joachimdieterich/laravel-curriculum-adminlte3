@extends('layouts.master')
@section('title')
    {{ trans('global.period.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.period.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <periods></periods>
@endsection
