@extends('layouts.contentonly')
@section('title')

@endsection
@section('breadcrumb')
@endsection
@section('content')
    <leaflet-map
        :map="{{ $map }}"
    ></leaflet-map>
@endsection

