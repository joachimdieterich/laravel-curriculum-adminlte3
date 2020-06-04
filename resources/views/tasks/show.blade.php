@extends('layouts.master')
@section('title')
    {{ trans('global.task.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.task.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 col-sm-12">
        <div class="card card-primary">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fa fa-tasks mr-1"></i> {{ $task->title }}
                    </h5>
                </div>
                @can('task_edit')
                <div class="card-tools pr-2">
                    <a @click.prevent="$modal.show('task-modal', {'method': 'patch', 'id': '{{ $task->id }}'})" >
                        <i class="far fa-edit"></i>
                    </a> 
                </div>
                @endcan 
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <strong><i class="fa fa-map-marker mr-1"></i> {{ trans('global.date') }}</strong>
                <p class="text-muted">
                    {{ $task->start_date }}<br>
                    {{ $task->due_date }}<br>
                    
                </p>
                <hr>

                <strong><i class="fa fa-file-alt mr-1"></i> {{ trans('global.task.fields.description') }}</strong>
                <p class="text-muted">{!! $task->description !!}</p>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="float-left">
                    
                </div>
                <small class="float-right">
                    {{ $task->updated_at }}
                </small> 
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active show" href="#activity" data-toggle="tab">{{ trans('global.subscription-billing') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">{{ trans('global.history') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">{{ trans('global.settings') }}</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="activity">
                        <task-timeline :task="{{ $task }}"></task-timeline>           
                        
                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="timeline"><!-- The timeline -->
                        
                    </div><!-- /.tab-pane -->

                    <div class="tab-pane" id="settings">
                        Organisational Settings
                    </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div><!-- /.nav-tabs-custom -->
    </div>
</div>

<task-modal></task-modal>
@endsection
