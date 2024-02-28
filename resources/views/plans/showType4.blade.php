@extends('layouts.master')

@section('content')
{{-- <div class="card pb-3">
    <div class="card-header">
        <div class="card-title">
            {{ $plan->type->title }}</br>
<!--            <small>
                <i class="fa fa-calendar pr-1 text-muted"></i>
                {{ $plan->begin }}
                <i class="fa fa-calendar-check pl-2 pr-1 text-muted "></i>
                {{ $plan->end }}
            </small>
-->
        </div>
        @can('plan_edit')
            @if($plan->owner_id == auth()->user()->id)
            <div class="card-tools pr-2 no-print">
                <a onclick="window.print();" class="link-muted pr-4 pointer">
                    <i class="fa fa-print"></i>
                </a>
                 <a href="{{route('plans.edit', $plan->id) }}" class="link-muted">
                    <i class="fa fa-pencil-alt"></i>
                </a>
            </div>
                @endif
        @endcan
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <span class="col-12">
                 {!! $plan->description !!}
            </span>
<!--            <span class="col-sm-3 col-xs-12">
                <span class="row text-muted">
                    @if($plan->owner->contactdetail != null)
                    <span class="col-12 pb-3">
                        <a class="pr-2 text-decoration-none link-muted"
                           href="{{ $plan->owner->contactdetail->path() }}">
                            <i class="fa fa-graduation-cap "></i>
                            {{ trans('global.contactdetail.title_singular') }}: {{ $plan->owner->fullname() }}
                        </a>
                    </span>
                    @endif
                     <span  class="col-12 pb-1">
                        <i class="fa fa-calendar pr-1"></i>
                        {{ $plan->begin }}
                    </span>
                    <span class="col-12 pb-1">
                        <i class="fa fa-calendar-check pr-1"></i>
                        {{ $plan->end }}
                    </span>
                    <span class="col-12 pb-1">
                        <i class="fa fa-stopwatch pr-1"></i>
                        {{ $plan->duration }} {{trans('global.minutes')}}
                    </span>
                </span>
            </span>
-->
        </div>
    </div>

</div>
--}}
<plan :plan="{{ $plan }}"></plan>
@can('medium_create')
    <medium-create-modal></medium-create-modal>
@endcan

{{--@php
$period = Carbon\CarbonPeriod::create($plan->begin, $plan->end);
$today = Carbon\Carbon::today()->format('yy-m-d')
@endphp--}}

{{--@foreach ($period as $day)
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
                    "tasks" =>  $plan->tasks()->whereDate('start_date', $day->format('Y-m-d'))->get(),
                    "hide_due_date" => true
                ])
                @can('plan_edit')
                <div class="text-muted"
                     onclick="app.__vue__.$modal.show('task-modal',  { 'subscribable_type': 'App\\Plan', 'subscribable_id':  {{ $plan->id }} , 'start_date': '{{ $day }}',  'due_date': '{{ $plan->end }}'});">
                    <i class="fa fa-plus p-2"></i>{{ trans('global.task.create') }}
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
@endforeach--}}

    @can('plan_create')
        @if (Auth::user()->id ==  $plan->owner_id)
            <subscribe-modal></subscribe-modal>
            <subscribe-objective-modal></subscribe-objective-modal>
            <set-achievements-modal :users="{{ json_encode($users) }}"></set-achievements-modal>
        @endif
    @endcan

  {{--  <task-modal></task-modal>--}}
@endsection
