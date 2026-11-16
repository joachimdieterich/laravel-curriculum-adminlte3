@extends('layouts.master')
@section('content')
    <Videoconference
        :videoconference="{{ $videoconference }}"
        :user="{{ auth()->user() }}"
    />
@endsection