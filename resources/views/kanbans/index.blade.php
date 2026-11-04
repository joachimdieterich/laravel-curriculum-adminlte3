@extends('layouts.master')

@section('title')
    {{ trans('global.kanban.title') }}
@endsection

@section('content')
    <Kanbans/>
@endsection