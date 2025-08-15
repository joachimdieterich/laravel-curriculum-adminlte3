@extends('layouts.master')
@section('title')
    {{ trans('global.task.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.task.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <task :task="{{ $task }}"></task>
    </div>
</div>

<medium-modal></medium-modal>
<medium-create-modal></medium-create-modal>
<subscribe-objective-modal></subscribe-objective-modal>
<task-modal></task-modal>
@endsection
