<h1>{{$model->title}}</h1>
<small>{{ $model->type->title}} </small> 

<div class="row">
    <span class="col-12">
         {{ $model->description }}
    </span>
</div>
<hr>
<div class="row">
    <span class="col-md-4 col-sm-12">
        <i class="fa fa-calendar pr-1"></i>
        {{ $model->begin }}
    </span>        
    <span class="col-md-4 col-sm-12">
        <i class="fa fa-calendar-check pr-1"></i>
        {{ $model->end }}
    </span>        
    <span class="col-md-4 col-sm-12">
        <i class="fa fa-stopwatch pr-1"></i>
        {{ $model->duration }} {{trans('global.minutes')}}
    </span>        
</div>
@php
$period = Carbon\CarbonPeriod::create($model->begin, $model->end);
$today = Carbon\Carbon::today()->format('yy-m-d')
@endphp

@foreach ($period as $day)
    @if($day->isWeekday() == true)
    
        @if($day->format('yy-m-d') == $today)
            @php $class = 'card-secondary'; @endphp
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
                    "tasks" =>  $model->tasks()->whereDate('start_date', $day->format('yy-m-d'))->get(),
                    "hide_due_date"  => true
                ])
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