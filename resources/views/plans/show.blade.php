@extends('layouts.master')

@section('title')
    {{ trans('global.plan.title_singular') }}
    @can('plan_create')
        @if (Auth::user()->id ==  $plan->owner_id)
            <button class="btn btn-flat"
                    onclick="app.__vue__.$modal.show('subscribe-modal',  {'modelId': {{ $plan->id }}, 'modelUrl': 'plan' });">
                <i class="fa fa-share-alt text-secondary"></i>
            </button>
        @endif
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.plan.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @switch($plan->type_id)
        @case(1)
            @include ('plans.showType1', [
               'plan' =>  $plan,
               'buttonText' => trans('global.plan.create')
           ])
            @break

        @case(4)
            @include ('plans.showType4', [
               'plan' =>  $plan,
               'buttonText' => trans('global.plan.create')
           ])
            @break

        @default

    @endswitch


@endsection
