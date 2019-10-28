@extends('layouts.master')
@section('title')
    {{ trans('global.add') }} {{ trans('global.user.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.add') }} {{ trans('global.user.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("users.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            @include('users.form', [
                'buttonText' => 'Create User'
            ])
        </form>
    </div>
</div>

@endsection