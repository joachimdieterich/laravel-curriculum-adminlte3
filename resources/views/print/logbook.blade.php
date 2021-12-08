<h3>{{ $logbook->title }}</h3>
@if($input['showDescription'] == "true")
    {!! $logbook->description !!}
@endif
@foreach($logbook->entries->where('begin', '>=', $input['begin'])->where('end', '<=', $input['end']) AS $entry)
    @include('print.logbookEntry', ['model' => $entry, 'input' => $input])
@endforeach
