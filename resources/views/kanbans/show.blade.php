@extends('layouts.master')

@section('contributors')
    <div id="contributors"></div>
@endsection

@section('title')
    <title-component></title-component>
@endsection

@section('content')
    <div class="h-100">
        <kanban
            :editable="{{ $may_edit ? 'true' : 'false' }}"
            :favourable="{{ $may_favour ? 'true' : 'false' }}"
            :websocket="{{ $is_websocket_active ? 'true' : 'false' }}"
            ref="kanbanBoard"
            :initial-kanban="{{ $kanban }}"
        ></kanban>
    </div>
@endsection
