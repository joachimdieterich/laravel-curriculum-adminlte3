<div style="
    position: relative;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.25rem;
    padding: 10px;
    margin-bottom: 10px;">
    <div>
        <h3 style="float: left; margin-top:10px">{{ $model->title }}</h3>
        <small style="float: right;">
            {{ Carbon\Carbon::parse($model->begin)->format('d.m.Y H:i') }}
            <br> {{ Carbon\Carbon::parse($model->end)->format('d.m.Y H:i') }}
        </small>
    </div>
    <hr style="border-top: thin gray; clear: both;">

    @if($input['showDescription'] == "true")
        {!! $model->description !!}
    @endif

    @can('content_access')
        @if($input['showContents'] == "true")
            @foreach($model->contents as $content)
                @if ($loop->first)
                    <h4 style="background-color: whitesmoke; padding: 5px">
                        {{ trans('global.content.title') }}
                    </h4>
                @endif

                <b>{{ $content->title }}</b><br>
                {!! $content->content !!}

                @if (!$loop->last)
                    <hr style="border-top: thin gray;">
                @endif
            @endforeach
        @endif
    @endcan

    @can('medium_access')
        @if($input['showMedia'] == "true")
            @foreach($model->media as $media)
                @if ($loop->first)
                    <h4 style="background-color: whitesmoke; padding: 5px">
                        {{ trans('global.media.title') }}
                    </h4>
                @endif

                {{ $media->title }}
                @if (!$loop->last)
                    <hr style="border-top: thin gray;">
                @endif

            @endforeach
        @endif
    @endcan

    @can('reference_access')
        @if($input['showReferences'] == "true")
        <!--
        <h4>{{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}</h4>
        -->
        @endif
    @endcan

    @can('task_access')
        @if($input['showTasks'] == "true")
        <!--<h4>{{ trans('global.task.title') }}</h4>
        @foreach($model->taskSubscription as $task)
            - {{ $task->task->title }}
                <hr>
            @endforeach-->
                @endif
                @endcan

                @can('absence_access')
                    @if($input['showAbsences'] == "true")
                    <!--<h4>{{ trans('global.absences.title') }}</h4>
        @foreach($model->absences as $absence)
                        - {{ $absence->absent_user->fullname() }}: {{ $absence->reason }}<br>
            <hr>
        @endforeach-->
                            @endif
                            @endcan
</div>
