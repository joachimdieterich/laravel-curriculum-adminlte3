@extends('layouts.contentonly')
@section('content')
    <leaflet-map
        :map="{{ $map }}"
        :editable="{{ $editable ? 'true' : 'false' }}"
    ></leaflet-map>
@endsection