@extends('layouts.master')
@section('title')
    {{ trans('global.task.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.task.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('user_create')
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
@endcan
<div class="row">
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center"><i class="fa fa-plus"></i></div>
            </div>
        </div>   
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center"><i class="fa fa-plus"></i></div>
            </div>
        </div>   
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center"><i class="fa fa-plus"></i></div>
            </div>
        </div>   
    </div>
</div>

<div class="card">
    <div class="card-body">
        
            <div class="col-4">
                @include('tasks.tasklist', [
                "tasks" =>  $tasks,
            ])
            </div>
            <div class="col-4">
                @include('tasks.tasklist', [
                "tasks" =>  $tasks,
            ])
            </div>
        </div>
        @include('tasks.tasklist', [
                "tasks" =>  $tasks,
            ])
    
</div>
<task-modal></task-modal>
@endsection
 