@extends('layouts.master')
@section('title')
    {{ trans('global.user.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.user.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("users.store") }}" 
              method="POST" 
              enctype="multipart/form-data">
            @method('POST')
            @include('users.form', [
                'buttonText' => trans('global.user.create')
            ])
        </form>
    </div>
</div>

@endsection