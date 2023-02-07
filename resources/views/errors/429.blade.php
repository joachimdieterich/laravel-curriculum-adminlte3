@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', trans('global.code_429'))
