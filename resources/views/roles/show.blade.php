@extends('layouts.master')
@section('title')
    {{ trans('global.role.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active"> {{ trans('global.role.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 col-sm-12">
        <div class="card card-primary">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fas fa-user-tag mr-1"></i> {{ $role->title }}
                    </h5>
                </div>
                @can('role_edit')
                    <div class="card-tools pr-2">
                        <a href="{{ route('roles.edit', $role->id) }}">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                        @endcan
                    </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="float-left">

                </div>
                <small class="float-right">
                    {{ $role->updated_at }}
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-sm-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active show" href="#settings" data-toggle="tab">Settings</a>
                    </li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active show" id="settings" style="column-count: 3;">
                    @foreach($role->permissions as $id => $permissions)
                            <ul class="btn btn-block btn-default btn-xs">{{ $permissions->title }}</ul>
                    @endforeach
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
