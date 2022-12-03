@extends('layouts.master')
@section('title')
    {{ trans('global.subject.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.variantDefinitions.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("variantDefinitions.store") }}"
              method="POST"
              enctype="multipart/form-data">
            @include ('variantdefinitions.form', [
                'variantdefinition' => new App\VariantDefinition(),
                'buttonText' => trans('global.variantDefinitions.create')
            ])
        </form>
    </div>
</div>
@endsection
