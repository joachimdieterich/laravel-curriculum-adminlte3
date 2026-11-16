@extends('layouts.master')
@section('contributors')
    <div id="contributors"></div>
@endsection
@section('title')
    <title-component></title-component>
@endsection
@section('content')
    <div class="h-100">
        <Kanban
            :editable="{{ $may_edit ? 'true' : 'false' }}"
            :favourable="{{ $may_favour ? 'true' : 'false' }}"
            :websocket="{{ $is_websocket_active ? 'true' : 'false' }}"
            :initial-kanban="{{ $kanban }}"
        ></Kanban>
    </div>
@endsection