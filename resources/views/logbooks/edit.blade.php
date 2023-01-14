@extends('layouts.master')
@section('title')
    {{ trans('global.logbook.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.logbook.edit') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("logbooks.update", [$logbook->id]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @include('logbooks.form', [
                    'logbook'     => $logbook,
                    'buttonText'    => trans('global.logbook.edit')
                ])
            </form>
        </div>
    </div>

@endsection
