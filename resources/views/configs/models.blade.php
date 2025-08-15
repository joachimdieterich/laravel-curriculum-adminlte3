@extends('layouts.master')
@section('title')
    {{ trans('global.config.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> false, 'title'=> trans('global.config.title'), 'url' => "/configs"],
            ['active'=> true, 'title'=> trans('global.config.model_limiter_title')],
            ])}}"
    ></breadcrumbs>

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
