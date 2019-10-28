@extends('layouts.master')
@section('title')
    {{ trans('global.edit') }} {{ trans('global.user.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }} {{ trans('global.user.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.user.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("users.update", [$user->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('users.form', [
                'user' => $user,
                'buttonText' => 'Update User'
            ])
        </form>
    </div>
</div>

@endsection
