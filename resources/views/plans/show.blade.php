@extends('layouts.master')
@section('title')
    <title-component></title-component>
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
            <plan
                :plan="{{ $plan }}"
                :editable="{{ $editable ? 'true' : 'false' }}"
                :users="{{ json_encode($users) }}"
            ></plan>
            @break
        @default
    @endswitch
@endsection