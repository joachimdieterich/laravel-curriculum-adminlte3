@extends('layouts.master')
@section('title')
    <title-component
        :show-back-button="true"
        back-button-title="global.back_to_overview"
        back-button-url="/curricula/"
    />
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