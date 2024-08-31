@extends('layouts.master')
@section('title')
    {{ trans('global.task.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.task.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
{{--@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-task"
               class="btn btn-success"
               href="{{ route("tasks.create") }}"
               @click.prevent="$modal.show('task-modal', {'method': 'post', 'subscribable_type': 'App\\User', 'subscribable_id': '{{auth()->user()->id}}'})">
               {{ trans('global.task.create') }}
            </a>
        </div>
    </div>
@endcan--}}

<tasks></tasks>
{{--
@include('tasks.tasklist', [
        "tasks" =>  $tasks,
    ])
--}}

<!--<task-modal></task-modal>-->
@endsection
