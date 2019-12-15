@extends('layouts.master')
@section('title')
     {{ trans('global.edit') }} {{ trans('global.curriculum.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }} {{ trans('global.curriculum.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("curricula.update", $curriculum->id) }}" 
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