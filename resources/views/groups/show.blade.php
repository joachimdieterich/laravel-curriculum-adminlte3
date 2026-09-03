@extends('layouts.master')
@section('title')
    <title-component></title-component>
@endsection
@section('content')
    <group :group="{{ $group }}"></group>
@endsection