@extends('layouts.master')
@section('title')
    <title-component></title-component>
@endsection
@section('content')
    <meeting :meeting="{{ $meeting }}" ref="meetings"></meeting>
@endsection