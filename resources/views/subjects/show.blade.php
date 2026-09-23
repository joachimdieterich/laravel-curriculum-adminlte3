@extends('layouts.master')
@section('title')
    {{ trans('global.subject.title_singular') }}
@endsection
@section('content')
    <Subject :subject="{{ $subject }}"/>
@endsection