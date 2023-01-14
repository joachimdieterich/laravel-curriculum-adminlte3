@extends('layouts.master')
@section('title')
    {{ trans('global.subject.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.subject.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
   <div class="card-body">
        <form action="{{ $subject->path() }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('subjects.form', [
                'subject' => $subject,
                'buttonText' => trans('global.subject.edit')
            ])
        </form>
    </div>
</div>

@endsection
