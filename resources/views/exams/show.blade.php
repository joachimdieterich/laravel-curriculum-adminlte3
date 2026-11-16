@extends('layouts.master')
@section('title')
    <div>
        <h4>Tool: {{  isset($exam) ? $exam->tool : 'tool' }}</h4>
    </div>
@endsection
@section('content')
    <Exam :exam="{{ $exam }}"></Exam>
@endsection