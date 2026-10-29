@extends('layouts.master')
@section('title')
    {{ trans('global.config.title_singular') }}
@endsection
@section('content')
    <config :config="{{ $config }}"></config>
@endsection