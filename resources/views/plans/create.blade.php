@extends('layouts.master')
@section('title')
    {{ trans('global.plan.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.plan.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("plans.store") }}"
              method="POST"
              enctype="multipart/form-data">
            @include ('plans.form', [
                'plan' =>  $plan,
                'types' => $types,
                'buttonText' => trans('global.plan.create')
            ])
        </form>
    </div>
</disv>
@endsection
