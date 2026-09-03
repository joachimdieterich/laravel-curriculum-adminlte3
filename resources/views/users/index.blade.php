@extends('layouts.master')
@section('title')
    {{ trans('global.user.title') }}
@endsection
@section('content')
    <users create_label_field="create"></users>
@endsection