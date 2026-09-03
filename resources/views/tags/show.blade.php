@extends('layouts.master')
@section('title')
    {{ trans('global.tag.title_singular') }}
@endsection
@section('content')
    <tag :tag="{{ $tag }}"></tag>
@endsection