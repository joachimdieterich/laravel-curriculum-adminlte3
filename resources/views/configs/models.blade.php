@extends('layouts.master')
@section('title')
    {{ trans('global.config.title') }}
@endsection
@section('content')
    <model-limiter
        model="logbook"
        :roles="{{ $roles }}"
        :initial_configs="{{ $configs }}"
        key_value="logbook_limiter"
        referenceable_type="App\Role"
    ></model-limiter>
@endsection