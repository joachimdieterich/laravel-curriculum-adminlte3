@extends('layouts.master')
@section('title')
    {{ trans('global.period.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active"> {{ trans('global.period.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 col-sm-12">
        <div class="card card-primary">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fa fa-history mr-1"></i> {{ $period->title }}
                    </h5>
                </div>
                @can('period_edit')
                <div class="card-tools pr-2">
                    <a href="{{ route('periods.edit', $period->id) }}" >
                        <i class="far fa-edit"></i>
                    </a>
                </div>
                @endcan
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <strong><i class="fa fa-calendar mr-1"></i> {{ trans('global.period.title_singular') }}</strong>

                <p class="text-muted">
                    {{ $period->begin }} - {{ $period->end }}
                </p>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="float-left">

                </div>
                <small class="float-right">
                    {{ $period->updated_at }}
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-sm-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active show" href="#activity" data-toggle="tab">Activity</a></li>
                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="activity">

                        Activity Tab

                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="timeline">
                        <!-- The timeline -->
                        timeline
                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="settings">
                        Organisational Settings
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>

</div>
@endsection
