@extends('layouts.master')
@section('title')
    {{ trans('global.objectiveType.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.objectiveType.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
   <div class="card-body">
        <form action="{{ $objectiveType->path() }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('objectivetypes.form', [
                'objectivetype' => $objectiveType,
                'buttonText' => trans('global.objectiveType.edit')
            ])
        </form>
    </div>
</div>

@endsection
