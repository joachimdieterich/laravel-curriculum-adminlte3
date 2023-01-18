@extends('layouts.master')
@section('title')
    {{ trans('global.organization.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.organization.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 col-sm-12">
        <div class="card card-primary">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fa fa-university mr-1"></i> {{ $organization->title }}
                    </h5>
                </div>
                @can('organization_edit')
                <div class="card-tools pr-2">
                    <a href="{{ route('organizations.edit', $organization->id) }}" >
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </div>
                @endcan
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <strong><i class="fas fa-signal mr-1"></i> {{ trans('global.organizationtype.title_singular') }}</strong>
                <p class="text-muted">
                    {{ $organization->type->title }}
                </p>
                <hr>

                <strong><i class="fa fa-map-marker mr-1"></i> {{ trans('global.place') }}</strong>
                <a class="pull-right link-muted"
                   href="{{ route('organizations.editAddress', $organization->id) }}" >
                    <i class="fas fa-pencil-alt"></i>
                </a>
                <p class="text-muted">
                    {{ $organization->street }}<br>
                    {{ $organization->postcode }} {{ $organization->city }}<br>
                    {{ $organization->state->lang_de }}, {{ $organization->country->lang_de }}
                </p>
                <hr>

                <strong><i class="fa fa-phone mr-1"></i> {{ trans('global.contactdetail.title_singular') }}</strong>
                <p class="text-muted">
                    {{ trans('global.organization.fields.phone') }}: {{ $organization->phone }}<br>
                    {{ trans('global.organization.fields.email') }}: {{ $organization->email }}
                </p>
                <hr>

                <strong><i class="fa fa-graduation-cap mr-1"></i> {{ trans('global.lms.title_singular') }}-URL</strong>
                <a class="pull-right link-muted"
                   href="{{ route('organizations.editLmsUrl', $organization->id) }}">
                    <i class="fas fa-pencil-alt"></i>
                </a>
                <p class="text-muted">
                    {{ $organization->lms_url }}
                </p>
                <hr>

                <strong><i class="fa fa-file-alt mr-1"></i> {{ trans('global.organization.fields.description') }}
                </strong>
                <p class="text-muted">{!! $organization->description !!}</p>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="float-left">
                    <span
                        class="btn-xs btn-block btn-{{$status_definitions[$organization->status_id]->color_css_class}} pull-right">{{$status_definitions[$organization->status_id]->lang_de}}</span>
                </div>
                <small class="float-right">
                    {{ $organization->updated_at }}
                </small>
            </div>
        </div>
    </div>

<!--    <div class="col-lg-8 col-sm-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active show" href="#activity" data-toggle="tab">Activity</a></li>
                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
            </div>&lt;!&ndash; /.card-header &ndash;&gt;
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="activity">

                        Activity Tab

                    </div>&lt;!&ndash; /.tab-pane &ndash;&gt;
                    <div class="tab-pane" id="timeline">&lt;!&ndash; The timeline &ndash;&gt;
                        timeline
                    </div>&lt;!&ndash; /.tab-pane &ndash;&gt;

                    <div class="tab-pane" id="settings">
                        Organisational Settings
                    </div>&lt;!&ndash; /.tab-pane &ndash;&gt;
                </div>&lt;!&ndash; /.tab-content &ndash;&gt;
            </div>&lt;!&ndash; /.card-body &ndash;&gt;
        </div>&lt;!&ndash; /.nav-tabs-custom &ndash;&gt;
    </div>-->
</div>
@endsection
