@extends('layouts.master')
@section('title')
     {{ trans('global.curriculum.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ $curriculum->path() }}" 
              method="POST" 
              enctype="multipart/form-data">
            @method('PATCH')
            @include ('curricula.form', [
                'curriculum' => $curriculum,
                'buttonText' =>  trans('global.save')
            ]) 
        </form>
    </div>
</div>
@endsection