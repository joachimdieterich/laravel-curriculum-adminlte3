<h1>{{ trans('global.glossar.title_singular') }}</h1>
@foreach($entries as $entry)
    <p>
        <strong class="lead">{{ $entry->title }}</strong>
        {!! $entry->content !!}
    </p>
@endforeach