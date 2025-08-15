@extends('errors::minimal')

@section('title', __('Unprocessable Entity'))
@section('code', '422')
@section('message', trans('global.code_422'))
