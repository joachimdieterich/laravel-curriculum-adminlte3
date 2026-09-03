@extends('layouts.master')
@section('title')
    <title-component
        :show-back-button="true"
        back-button-title="global.back_to_curriculum"
        :back-button-url="{{ json_encode('/curricula/' . $objective->curriculum_id) }}"
    ></title-component>
@endsection
@section('content')
    <objective
        ref="curriculumView"
        :repository="{{ $repository }}"
        :objective="{{ $objective }}"
        :editable="{{ json_encode($editable) }}"
    ></objective>
@endsection