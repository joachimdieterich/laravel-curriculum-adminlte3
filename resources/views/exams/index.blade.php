@extends('layouts.master')
@section('title')
    {{ trans('global.exam.title') }}
@endsection
@section('content')
    <exams_list></exams_list>
@endsection