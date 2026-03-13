@extends('layouts.master')
@section('content')
    <home role="{{ auth()->user()->role()->title }}"></home>
@endsection