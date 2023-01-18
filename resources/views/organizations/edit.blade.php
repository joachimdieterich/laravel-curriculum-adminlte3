@extends('layouts.master')
@section('title')
    {{ trans('global.organization.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.organization.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("organizations.update", [$organization->id]) }}"
              method="POST"
              enctype="multipart/form-data">
            @method('PATCH')
            @include('organizations.form', [
                'organization' => $organization,
                'status_definitions' => $status_definitions,
                'buttonText' => trans('global.organization.edit')
            ])
        </form>
    </div>
</div>

@endsection
