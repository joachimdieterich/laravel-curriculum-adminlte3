@extends('layouts.master')
@section('title')
    {{ trans('global.subject.title_singular') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.subject.title_singular'), 'url' => "/periods"],
            ['active'=> true, 'title'=> $subject->title]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
<subject
 :subject="{{ $subject }}"></subject>
@endsection
