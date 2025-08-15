@extends('layouts.embed')

@section('content')
    <events
        class="pt-2"
        search="{{ app('request')->input('search') }}"
        title="{{ app('request')->input('title') }}"
        backlinktitle="{{ app('request')->input('backlinktitle') }}"
        backlinkurl="{{ app('request')->input('backlinkurl') }}"
        eventLinkTitle = "{{ App\Config::where('key', 'eventLinkTitle')->get()->first()->value }}"
        eventLinkDescription = "{{ App\Config::where('key', 'eventLinkDescription')->get()->first()->value }}"
        eventLinkUrl = "{{ App\Config::where('key', 'eventLinkUrl')->get()->first()->value }}"
        eventSearchTag = "{{ json_encode(json_decode(App\Config::where('key', 'eventSearchTag')->get()->first()->value, true)[app('request')->input('tag')] ?? '') }}"
    ></events>
@endsection