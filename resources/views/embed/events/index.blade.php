@extends('layouts.embed')

@section('content')
    <events class="pt-2"
            search="{{ app('request')->input('search') }}">
    </events>
@endsection
