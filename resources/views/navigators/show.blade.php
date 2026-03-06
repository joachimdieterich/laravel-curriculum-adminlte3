@extends('layouts.contentonly')

@section('breadcrumb')
    @if (Auth::user()->id == config('app.guest_user_id'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> Str::limit($navigator->title, 10) ]
        ])}}"
        ></breadcrumbs>
    @else
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> false, 'title'=> trans('global.navigator.title'), 'url' => '/navigators' ],
            ['active'=> true, 'title'=> $navigator->title ]
        ])}}"
    ></breadcrumbs>
    @endif
@endsection
@section('content')
    <navigator
        :navigator="{{ $navigator }}"
        :view="{{ $view ?? null }}">
    </navigator>
@endsection
