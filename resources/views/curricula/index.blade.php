@extends('layouts.master')
@section('title')
    {{ trans('global.curriculum.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.title') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    @can('curriculum_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("curricula.create") }}">
                    {{ trans('global.curriculum.create') }}
                </a>
                <a class="btn btn-success" href="{{ route("curricula.import") }}">
                    {{ trans('global.curriculum.import') }}
                </a>
            </div>
        </div>
    @endcan
    <curricula model-url="curricula"></curricula>
    @can('group_enrolment')
        <subscribe-modal
            canEditCheckbox="false"></subscribe-modal>
    @endcan

@endsection
