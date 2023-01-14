@extends('layouts.master')
@section('title')
    {{ trans('global.config.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.config.edit') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("configs.update", [$config->id]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @include('configs.form', [
                    'config' => $config,
                    'buttonText' => trans('global.config.edit')
                ])
            </form>
        </div>
    </div>

@endsection
