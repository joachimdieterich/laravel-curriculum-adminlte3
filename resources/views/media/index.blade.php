@extends('layouts.master')
@section('title')
    {{ trans('global.media.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.medium.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <media-index></media-index>
   {{--
<table id="media-datatable" class="table table-hover datatable">
    <thead>
    <tr>
        <th></th>
        <th>{{ trans('global.media.fields.title') }}</th>
        <th>{{ trans('global.media.fields.description') }}</th>
        <th>{{ trans('global.media.fields.size') }}</th>
        <th>{{ trans('global.media.fields.created_at') }}</th>
        <th>{{ trans('global.media.fields.public') }}</th>
        <th>{{ trans('global.datatables.action') }}</th>
    </tr>
    </thead>
</table>
--}}
<medium-modal></medium-modal>

@endsection
