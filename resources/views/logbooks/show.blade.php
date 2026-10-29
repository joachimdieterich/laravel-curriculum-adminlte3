@extends('layouts.master')
@section('title')
    <title-component></title-component>
@endsection
@section('content')
    <logbook
        :logbook="{{ $logbook }}"
        :period="{{App\Period::find(auth()->user()->current_period_id)}}"
    ></logbook>
@endsection