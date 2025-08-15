@extends('layouts.master')
@section('title')
    {{ trans('global.grade.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.grade.edit') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ $grade->path() }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                @include('grades.form', [
                    'grade' => $grade,
                    'buttonText' => trans('global.grade.edit')
                ])
            </form>
        </div>
    </div>

@endsection
