@extends('layouts.master')
@section('title')
    {{ trans('global.config.title') }}

@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item "><a href="/configs">{{ trans('global.config.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.config.model_limiter_title') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection

@section('content')
    <model-limiter
        model="logbook"
        :roles="{{ $roles }}"
        :initial_configs="{{ $configs }}"
        key_value="logbook_limiter"
        referenceable_type="App\Role"
    ></model-limiter>
@endsection


