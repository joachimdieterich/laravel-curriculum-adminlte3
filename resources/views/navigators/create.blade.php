@extends('layouts.master')
@section('title')
    {{ trans('global.navigator.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.navigator.create') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("navigators.store") }}"
                  method="POST"
                  enctype="multipart/form-data">
                @include ('navigators.form', [
                    'navigator' => new App\Navigator,
                    'buttonText' => trans('global.navigator.create')
                ])
            </form>
        </div>
    </div>
@endsection
