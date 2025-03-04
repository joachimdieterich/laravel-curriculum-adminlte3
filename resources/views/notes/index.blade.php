@extends('layouts.master')
@section('title')
    {{ trans('global.note.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.note.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
<notes></notes>
@endsection
