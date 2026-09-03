@extends('layouts.master')
@section('title')
    {{ trans('global.user.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.user.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <users create_label_field="create"></users>
@endsection