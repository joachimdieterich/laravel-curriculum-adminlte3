<h3 >{{ $model->title }}</h3>
<small>{{ $model->begin }} {{ $model->end }}</small>

<h4>{{ trans('global.description') }}</h4>
{!! $model->description !!}

<h4>{{ trans('global.media.title') }}</h4>
@foreach($model->media as $media)
    - {{ $media->title }}
    <hr>
@endforeach

<h4>{{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}</h4>


<h4>{{ trans('global.content.title') }}</h4>
@foreach($model->contents as $content)
    <b>{{ $content->title }}</b><br>
    {!! $content->content !!}
    <hr>
@endforeach

<h4>{{ trans('global.task.title') }}</h4>
@foreach($model->taskSubscription as $task)
    - {{ $task->task->title }}
    <hr>
@endforeach

<h4>{{ trans('global.absences.title') }}</h4>
@foreach($model->absences as $absence)
    - {{ $absence->absent_user->fullname() }}: {{ $absence->reason }}<br>
    <hr>
@endforeach
