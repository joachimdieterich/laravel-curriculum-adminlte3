@extends('layouts.master')
@section('title')
    {{ trans('global.role.title_singular') }}
@endsection
@section('content')
    <Role
        :role="{{ $role }}"
        :all-permissions="{{ $allPermissions }}"
    />
@endsection