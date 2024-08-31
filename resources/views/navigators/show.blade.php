@extends('layouts.master')

@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> false, 'title'=> trans('global.navigator.title'), 'url' => '/navigators' ],
            ['active'=> true, 'title'=> $navigator->title ]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <navigator
        :navigator="{{ $navigator }}"
        :view="{{ $view ?? null }}">
    </navigator>
@endsection
