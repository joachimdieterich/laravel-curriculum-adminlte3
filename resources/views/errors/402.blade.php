@extends('errors::minimal')

@section('title', __('Limit reached'))
@section('code', '402')
@section('message', __($exception->getMessage() ?: 'global.code_402'))
