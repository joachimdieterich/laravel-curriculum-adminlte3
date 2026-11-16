@extends('layouts.master')
@section('title')
    {{ trans('global.grade.title_singular') }}
@endsection
@section('content')
    <Grade :grade="{{ $grade }}"/>
@endsection