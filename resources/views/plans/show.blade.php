@extends('layouts.master')

@section('title')
    {{ $plan->title }}
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
                :editable="{{ $editable ? 'true' : 'false' }}"></plan>
           {{-- @can('medium_create')
                <medium-create-modal></medium-create-modal>
            @endcan
--}}
            @can('plan_create')
                @if ($editable)
               {{--     <subscribe-modal></subscribe-modal>
                    <subscribe-objective-modal></subscribe-objective-modal>
                    <set-achievements-modal :users="{{ json_encode($users) }}"></set-achievements-modal>--}}
                @endif
            @endcan

            @break
        @default
    @endswitch
@endsection
