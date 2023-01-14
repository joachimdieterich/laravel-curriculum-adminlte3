@extends('layouts.master')
@section('title')
    {{ trans('global.curriculum.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.edit') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ $curriculum->path() }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @include ('curricula.form', [
                    'curriculum' => $curriculum,
                    'buttonText' =>  trans('global.save')
                ])
            </form>
        </div>
    </div>
@endsection
