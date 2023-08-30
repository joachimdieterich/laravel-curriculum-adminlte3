@extends('layouts.master')
@section('title')
    {{ trans('global.training.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item ">
        <a href="/plans/{{$training->subscriptions()->first()->subscribable()->get()->first()->plan_id}}">
            {{$training->subscriptions()->first()->subscribable()->get()->first()->title}}
        </a>
    </li>
    <li class="breadcrumb-item active"> {{ $training->title }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <training :training="{{ $training }}"></training>
    </div>

</div>
@can('medium_create')
    <medium-create-modal></medium-create-modal>
@endcan
@endsection
