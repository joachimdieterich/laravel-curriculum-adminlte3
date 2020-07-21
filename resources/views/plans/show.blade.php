@extends('layouts.master')
@section('title')
    {{ trans('global.plan.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.plan.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <h5 class="m-0">
                <i class="far fa-clipboard mr-1"></i> 
                {{ $plan->title }}
            </h5>
            <small>{{ $plan->type->title}} </small> 
        </div>
@can('plan_edit')
        <div class="card-tools pr-2 no-print">   
            <a class="pr-2 text-decoration-none link-muted" href="/contacts/{{ $plan->owner->id }}">
                <i class="fa fa-chalkboard-teacher "></i>
                 {{ $plan->owner->fullname() }}
            </a>
            <a href="{{route('plans.edit', $plan->id) }}" >
                <i class="far fa-edit"></i>
            </a>  
            <a href="{{ route('print.model', ['model' => 'App\Plan', 'id' =>  $plan->id]) }}" >
                <i class="fa fa-print"></i>
            </a>
        </div>
@endcan

    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <span class="col-12">
                 {{ $plan->description }}
            </span>
        </div>
        <hr>
        <div class="row">
            <span class="col-md-4 col-sm-12">
                <i class="fa fa-calendar pr-1"></i>
                {{ $plan->begin }}
            </span>        
            <span class="col-md-4 col-sm-12">
                <i class="fa fa-calendar-check pr-1"></i>
                {{ $plan->end }}
            </span>        
            <span class="col-md-4 col-sm-12">
                <i class="fa fa-stopwatch pr-1"></i>
                {{ $plan->duration }} {{trans('global.minutes')}}
            </span>        
        </div>
       
    </div>
    
</div>
@php
$period = Carbon\CarbonPeriod::create($plan->begin, $plan->end);
$today = Carbon\Carbon::today()->format('yy-m-d')
@endphp

@foreach ($period as $day)
    @if($day->isWeekday() == true)
    
        @if($day->format('yy-m-d') == $today)
            @php $class = 'card-secondary card-outline'; @endphp
        @else
            @php $class = ''; @endphp
        @endif
        <div class="card {{ $class }}">
            <div class="card-header">
                <i class="fas fa-calendar-day mr-1"></i> 
                {{ $day->locale('de')->dayName }}, {{ $day->isoFormat('LL') }}
            </div>
            <div class="card-body py-2">
                @include('tasks.tasklist', [
                    "tasks" =>  $plan->tasks()->whereDate('start_date', $day->format('yy-m-d'))->get(),
                    "hide_due_date"  => true
                ])
                @can('plan_edit')
                <div class="text-center py-1"
                     onclick="app.__vue__.$modal.show('task-modal',  { 'subscribable_type': 'App\\Plan', 'subscribable_id':  {{ $plan->id }} , 'start_date': '{{ $day }}',  'due_date': '{{ $plan->end }}'});">
                    <i class="fa fa-plus-circle fa-1x"></i>
                </div>
                @endcan
            </div>
            
        </div>
    @else
        <div class="card bg-transparent">
            <div class="card-header">              
                <i class="fa fa-hiking mr-1"></i> 
                {{ $day->locale('de')->dayName }}, {{ $day->isoFormat('LL') }}
            </div>
           
        </div>
    @endif
@endforeach

<task-modal></task-modal>

@endsection
