@extends('layouts.master')
@section('title')
    {{ trans('global.objectiveType.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.objectiveType.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 col-sm-12">
        <div class="card card-primary">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fa fa-history mr-1"></i> {{ $objectiveType->title }}
                    </h5>
                </div>
                @can('objectivetype_edit')
                <div class="card-tools pr-2">
                    <a href="{{ route('objectiveTypes.edit', $objectiveType->id) }}" >
                       <i class="far fa-edit"></i>
                    </a>
                </div>
                @endcan
            </div>
            <!-- /.card-header -->
            <div class="card-body">

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="float-left"></div>
                <small class="float-right">
                    {{ $objectiveType->updated_at }}
                </small>
            </div>
        </div>
    </div>

</div>
@endsection
