@extends('layouts.master')
@section('title')
    {{ trans('global.curriculum.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("curricula.store") }}" 
              method="POST" 
              enctype="multipart/form-data">
            @include ('curricula.form', [
                'curriculum' => new App\Curriculum,
                'buttonText' =>  trans('global.curriculum.create')
            ]) 
        </form>
    </div>
</div>
@endsection