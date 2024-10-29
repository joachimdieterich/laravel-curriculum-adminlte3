@extends('layouts.master')
@section('title')
    {{ trans('global.training.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.plan.title'), 'url' => "/plans"],
            ['active'=> true, 'title'=> $training->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <training :training="{{ $training }}"></training>
    </div>
</div>
@endsection
