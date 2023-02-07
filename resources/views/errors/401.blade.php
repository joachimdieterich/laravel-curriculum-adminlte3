@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', trans('global.code_401'))
