@extends('layouts.master')
@section('title')
    {{ trans('global.subject.title_singular') }}
@endsection
@section('content')
    <subject :subject="{{ $subject }}"></subject>
@endsection