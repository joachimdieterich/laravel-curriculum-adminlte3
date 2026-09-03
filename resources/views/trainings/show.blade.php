@extends('layouts.master')
@section('title')
    <title-component/>
@endsection
@section('content')
    <Training :training="{{ $training }}"/>
@endsection