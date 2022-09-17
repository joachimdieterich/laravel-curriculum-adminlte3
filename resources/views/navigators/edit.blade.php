@extends('layouts.master')
@section('title')
    {{ trans('global.navigator.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.navigator.edit') }}</li>
    <li class="breadcrumb-item ">
        <a href="/documentation"
           class="text-black-50"
           aria-label="{{ trans('global.documentation') }}">
            <i class="fas fa-question-circle"></i>
        </a>
    </li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("navigators.update", [$navigator->id]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @include('navigators.form', [
                    'navigator'     => $navigator,
                    'organizations' => $organizations,
                    'buttonText'    => trans('global.navigator.edit')
                ])
            </form>
        </div>
    </div>

@endsection
