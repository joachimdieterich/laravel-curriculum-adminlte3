@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.title_singular') }}
@endsection
@section('content')
    <certificate :certificate="{{ $certificate }}"></certificate>
@endsection