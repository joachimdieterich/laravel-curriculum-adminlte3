@extends('layouts.master')
@section('title')
    {{ trans('global.logbook.title') }}
@endsection
@section('content')
    <logbooks create_label_field="create"></logbooks>
@endsection