@extends('layouts.master')
@section('title')
    {{ trans('global.role.title_singular') }}
@endsection
@section('content')
    <role
        :role="{{ $role }}"
        :all-permissions="{{ $allPermissions }}"
    ></role>
@endsection