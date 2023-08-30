@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', trans($exception->getMessage() ?: 'global.code_503'))
