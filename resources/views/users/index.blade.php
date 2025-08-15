@extends('layouts.master')
@section('title')
    <title-component>
        <template v-slot:title>
            {{ trans('global.user.title') }}
        </template>
        <template v-slot:toolbar></template>
    </title-component>
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.user.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <users create_label_field="create"></users>
@endsection
