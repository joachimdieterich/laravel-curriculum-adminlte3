<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body >
    <h1>{{ $curriculum->title }}</h1>
    {!! $curriculum->description !!}
    @foreach($curriculum->contents as $content)
        <p  style="page-break-before: always">
            <strong class="lead">{{ $content->title }}</strong>
            {!! $content->content !!}
        </p>
    @endforeach

    @foreach($curriculum->terminalObjectives as $terminalObjective)
        <span class="box" >
            {!! $terminalObjective->title !!}<hr>
            {!! $terminalObjective->description !!}
        </span>

        @foreach($terminalObjective->enablingObjectives as $enablingObjectives)
            <span class="box">
                {!! $enablingObjectives->title !!}<hr>
                {!! $enablingObjectives->description !!}
            </span>
        @endforeach
    @endforeach
</body>

</html>
