@extends('layouts.master')

@section('title')
    <title-component></title-component>
@endsection

@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.plan.title_singular'), 'url' => "/plans"],
            ['active'=> true, 'title'=> $plan->title]
        ])}}"
    ></breadcrumbs>
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