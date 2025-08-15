@extends('layouts.master')
@section('title')
    {{ trans('global.variantDefinitions.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.variantDefinitions.title_singular'), 'url' => "/variantDefinitions"],
            ['active'=> true, 'title'=> $variantDefinition->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <variant-definition
        :variant-definition="{{ $variantDefinition }}"></variant-definition>
@endsection
