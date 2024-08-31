@extends('layouts.master')
@section('title')
    {{ trans('global.grade.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.grade.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
<grades></grades>
@endsection

