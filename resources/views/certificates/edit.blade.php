@extends('layouts.master')
@section('title')
    {{ trans('global.edit') }} {{ trans('global.certificate.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }} {{ trans('global.certificate.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("certificates.update", [$certificate->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
           <form method="POST" 
              action="{{ $certificate->path() }}" >
            @method('PATCH')
            @include('certificates.form', [
                'certificate' => $certificate,
                'buttonText' => trans('global.edit').' '.trans('global.certificate.title_singular')
            ])
        </form>
    </div>
</div>

@endsection