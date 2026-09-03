@extends('layouts.master')
@section('title')
    {{ trans('global.period.title_singular') }}
@endsection
@section('content')
    <period :period="{{ $period }}"></period>
@endsection