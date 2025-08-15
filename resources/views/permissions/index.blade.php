@extends('layouts.master')
@section('title')
    {{ trans('global.permission.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.permission.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
<permissions></permissions>
@endsection
