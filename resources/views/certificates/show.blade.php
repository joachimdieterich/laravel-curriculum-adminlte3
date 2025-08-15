@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.certificate.title'), 'url' => "/certificates"],
            ['active'=> true, 'title'=> $certificate->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <certificate
        :certificate="{{ $certificate }}"
    ></certificate>
@endsection
