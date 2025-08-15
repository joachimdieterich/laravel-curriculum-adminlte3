@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.certificate.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    <div class="row">
        Todo Preview for
         {{ $certificate }}
        {!! $certificate->body !!}
    </div>

@endsection
