@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.title_singular') }}
@endsection
@section('content')
    <Certificate :certificate="{{ $certificate }}"/>
@endsection