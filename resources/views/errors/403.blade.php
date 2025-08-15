@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', trans($exception->getMessage() ?: 'global.code_403'))
