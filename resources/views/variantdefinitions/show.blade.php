@extends('layouts.master')
@section('title')
    {{ trans('global.variantDefinitions.title_singular') }}
@endsection
@section('content')
    <variant-definition :variant-definition="{{ $variantDefinition }}"></variant-definition>
@endsection