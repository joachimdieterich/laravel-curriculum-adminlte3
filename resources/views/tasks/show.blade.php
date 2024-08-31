@extends('layouts.master')
@section('title')
    {{ trans('global.task.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.task.title_singular'), 'url' => "/periods"],
            ['active'=> true, 'title'=> $task->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <task :task="{{ $task }}"></task>
@endsection
