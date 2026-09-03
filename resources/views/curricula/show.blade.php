@extends('layouts.master')
@section('title')
    <title-component></title-component>
@endsection
@section('contributors')
    <div id="contributors"></div>
@endsection
@section('content')
    <Curriculum
        :curriculum="{{ $curriculum }}"
        :course="{{ $course ?? json_encode((object)[]) }}"
        :settings="{{ $settings }}"
    />
    <div id="content_top_placeholder"></div>
@endsection