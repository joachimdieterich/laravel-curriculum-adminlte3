@extends('layouts.master')
@section('title')
    <title-component></title-component>
@endsection
@section('content')
    <Group :group="{{ $group }}"/>
@endsection