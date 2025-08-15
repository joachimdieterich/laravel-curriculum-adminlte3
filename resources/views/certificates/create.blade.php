@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.certificate.create') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="{{ route("certificates.store") }}">
                @include ('certificates.form', [
                    'certificate' => $certificate,
                    'buttonText' =>  trans('global.certificate.create')
                ])
        </form>
    </div>
</div>
@endsection
