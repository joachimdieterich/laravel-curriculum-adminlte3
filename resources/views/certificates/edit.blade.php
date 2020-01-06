@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.certificate.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form method="POST" 
            action="{{ $certificate->path() }}" >
            @method('PATCH')
            @include('certificates.form', [
                'certificate' => $certificate,
                'buttonText' => trans('global.certificate.edit')
            ])
        </form>
    </div>
</div>

@endsection