@extends((Auth::user()->id == env('GUEST_USER')) || $is_shared ? 'layouts.contentonly' : 'layouts.master')

@section('contributors')
    <div class="row">
        <div>
            <div id="contributors">
            </div>
        </div>
    </div>
@endsection
@section('breadcrumb')
    @if (Auth::user()->id == env('GUEST_USER'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active' => true, 'title'=> Str::limit($kanban->title, 10) ]
        ])}}"
        ></breadcrumbs>
    @else
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.kanban.title_singular'), 'url' => "/kanbans"],
            ['active'=> true, 'title'=> Str::limit($kanban->title, 10) ]
        ])}}"
        ></breadcrumbs>
    @endif
@endsection

@section('title')
    <title-component></title-component>
@endsection

@section('content')
    <div class="d-flex flex-fill h-print-auto" style="height: calc(100vh - 218px)">
        <kanban
            :editable="{{ $may_edit ? 'true' : 'false' }}"
            :websocket="{{ $is_websocket_active ? 'true' : 'false' }}"
            ref="kanbanBoard"
            :initial-kanban="{{ $kanban }}"></kanban>
    </div>
@endsection
