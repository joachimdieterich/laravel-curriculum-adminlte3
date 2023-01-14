@extends('layouts.master')
@section('title')
    {{ trans('global.plan.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.plan.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("plans.update", [$plan->id]) }}"
              method="POST"
              enctype="multipart/form-data">
            @method('PATCH')
            @include('plans.form', [
                'plan' => $plan,
                'types' => $types,
                'buttonText' => trans('global.plan.edit')
            ])
        </form>
    </div>
</div>

@endsection
