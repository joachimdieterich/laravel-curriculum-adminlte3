@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.certificate.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("certificates.store") }}" method="POST" enctype="multipart/form-data">
            <form
            method="POST"
            action="/certificates"
        >
            @include ('certificates.form', [
                'certificate' => new App\Curriculum,
                'buttonText' =>  trans('global.create'). ' ' .trans('global.certificate.title_singular')
            ]) 
        </form>
    </div>
</div>
@endsection