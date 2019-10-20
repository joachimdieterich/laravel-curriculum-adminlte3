@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.certificate.title_singular') }}
    </div>

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