@extends('layouts.master')
@section('title')
    {{ trans('global.organizationtype.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.organizationtype.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("organizationtypes.update", [$organizationtype->id]) }}"
              method="POST"
              enctype="multipart/form-data">
            @method('PATCH')
            @include('organizationtypes.form', [
                'organizationtype' => $organizationtype,
                'buttonText' => trans('global.organizationtype.edit')
            ])
        </form>
    </div>
</div>

@endsection
