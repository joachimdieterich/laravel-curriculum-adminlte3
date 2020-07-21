@extends('layouts.master')
@section('title')
     {{ $group->title }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ $group->title }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        @foreach($group->curricula as $id => $currculum)
            @include ('navigators.views.items.item', [ 'item' => $currculum, 'onclick' => "location.href='/curricula/{$currculum->id}';" ])
        @endforeach
<!--        <div class="card card-primary">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fa fa-users mr-1"></i> {{ $group->title }}
                    </h5>
                </div>
                @can('group_edit')
                <div class="card-tools pr-2">
                    <a href="{{ route('groups.edit', $group->id) }}" >
                       <i class="far fa-edit"></i>
                    </a> 
                </div> 
                @endcan
            </div>
             /.card-header 
            <div class="card-body"> 
                <strong><i class="fa fa-university mr-1"></i> {{ trans('global.organization.title_singular') }}</strong>

                <p class="text-muted">
                    {{ $group->organization->title }}
                </p>

                <hr>

                <strong><i class="fas fa-signal mr-1"></i> {{ trans('global.grade.title_singular') }}</strong>

                <p class="text-muted">
                    {{ $group->grade->title }}
                </p>

                <hr>

                <strong><i class="fa fa-history mr-1"></i> {{ trans('global.period.title_singular') }}</strong>

                <p class="text-muted">
                    {{ $group->period->title }}
                </p>

                <hr>

                <strong><i class="fa fa-th mr-1"></i> {{ trans('global.curriculum.title') }}</strong>

                <p class="text-muted">
                <ul>
                    @foreach($group->curricula as $id => $currculum)
                    <a class="btn-block text-muted" href="/curricula/{{ $currculum->id }}"><li>{{ $currculum->title }}</li></a>
                    @endforeach
                </ul>
                </p>

            </div>
             /.card-body 
            <div class="card-footer">
                <div class="float-left">

                </div>
                <small class="float-right">
                    {{ $group->updated_at }}
                </small> 
            </div>
        </div>-->
    </div>
    
<!--    <div class="col-lg-8 col-sm-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active show" href="#activity" data-toggle="tab">Activity</a></li>
                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
            </div> /.card-header 
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="activity">
                        Activity Tab
                    </div>
                     /.tab-pane 
                    <div class="tab-pane" id="timeline">
                         The timeline 
                        timeline
                    </div>
                     /.tab-pane 

                    <div class="tab-pane" id="settings">
                        Organisational Settings
                    </div>
                     /.tab-pane 
                </div>
                 /.tab-content 
            </div> /.card-body 
        </div>
         /.nav-tabs-custom 
    </div>-->
</div>
@endsection
