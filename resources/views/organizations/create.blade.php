@extends('layouts.master')
@section('title')
    {{ trans('global.create') }} {{ trans('global.organization.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create') }} {{ trans('global.organization.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("organizations.store") }}" method="POST" enctype="multipart/form-data">
            @include ('organizations.form', [
                'organization' => new App\Organization,
                'buttonText' => 'Create Institution'
            ])
        </form>
    </div>
</div>
@endsection