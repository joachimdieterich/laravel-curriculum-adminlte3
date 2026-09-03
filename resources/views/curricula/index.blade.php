@extends('layouts.master')
@section('title')
    {{ trans('global.curriculum.title') }}
@endsection
@section('content')
    <curricula model-url="curricula"></curricula>
@endsection