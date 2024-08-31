@extends('layouts.master')
@section('title')
    {{ trans('global.curriculum.title') }}
@endsection
@section('breadcrumb')
    @if (Auth::user()->id == env('GUEST_USER'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.curriculum.title_singular'), 'url' => "/navigators/" . Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id],
        ])}}"
        ></breadcrumbs>
    @else
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.curriculum.title')],
            ])}}"
        ></breadcrumbs>
    @endif
@endsection

@section('content')
   {{-- @can('curriculum_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("curricula.create") }}">
                    {{ trans('global.curriculum.create') }}
                </a>
                <a class="btn btn-success" href="{{ route("curricula.import") }}">
                    {{ trans('global.curriculum.import') }}
                </a>
            </div>
        </div>
    @endcan--}}
    <curricula model-url="curricula"></curricula>

@endsection
