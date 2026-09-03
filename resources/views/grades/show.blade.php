@extends('layouts.master')
@section('title')
    {{ trans('global.grade.title_singular') }}
@endsection
@section('content')
    <grade :grade="{{ $grade }}"></grade>
@endsection