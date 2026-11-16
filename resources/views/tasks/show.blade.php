@extends('layouts.master')
@section('title')
    {{ trans('global.task.title_singular') }}
@endsection
@section('content')
    <task :task="{{ $task }}"></task>
@endsection