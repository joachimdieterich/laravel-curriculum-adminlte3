@extends('layouts.master')
@section('title')
    <title-component></title-component>
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> $group->title ]
        ])}}"
    ></breadcrumbs>

@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <group
                :group="{{ $group }}">
            </group>
        </div>
    </div>
@endsection
