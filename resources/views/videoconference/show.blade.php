@extends('layouts.master')
@section('content')
    <videoconference
        :videoconference="{{ $videoconference }}"
        :user="{{auth()->user()}}"
    ></videoconference>
@endsection