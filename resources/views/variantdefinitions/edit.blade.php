@extends('layouts.master')
@section('title')
    {{ trans('global.variantDefinitions.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.variantDefinitions.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
   <div class="card-body">
        <form action="{{ $variantDefinition->path() }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('variantdefinitions.form', [
                'variantdefinitions' => $variantDefinition,
                'buttonText' => trans('global.variantDefinitions.edit')
            ])
        </form>
    </div>
</div>

@endsection
