@extends('layouts.master')
@section('title')
    {{ trans('global.period.title_singular') }}
@endsection
@section('content')
    <Period :period="{{ $period }}"/>
@endsection