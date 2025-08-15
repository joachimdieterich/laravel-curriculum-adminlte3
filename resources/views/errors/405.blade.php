@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '405')
@section('message', trans('global.code_405'))
