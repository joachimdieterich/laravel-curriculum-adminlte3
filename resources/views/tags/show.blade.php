@extends('layouts.master')
@section('title')
    {{ trans('global.tag.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.tag.title_singular'), 'url' => "/tags"],
            ['active'=> true, 'title'=> $tag->translated_tag]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <tag
        :tag="{{ $tag }}">
    </tag>
@endsection
