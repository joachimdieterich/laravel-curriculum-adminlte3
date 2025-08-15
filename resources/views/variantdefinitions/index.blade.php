@extends('layouts.master')
@section('title')
    {{ trans('global.variantDefinition.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.variantDefinition.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <variant-definitions></variant-definitions>
@endsection
