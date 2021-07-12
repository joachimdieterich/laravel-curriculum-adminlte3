@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    <small>{{ $kanban->title }} </small>
    @can('kanban_create')
    <button class="btn btn-flat"
            onclick="app.__vue__.$modal.show('subscribe-modal',  {'modelId': {{ $kanban->id }}, 'modelUrl': 'kanban' });">
        <i class="fa fa-share-alt text-secondary"></i>
    </button>
    @endcan
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/">{{ trans('global.home') }}</a>
        @endif
    </li>
    <li class="breadcrumb-item active">{{ trans('global.kanban.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection

@section('content')
<!-- {!! $kanban->description !!}-->
<div >
    @can('kanban_entry_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <button id="add-kanban-entry"
                   class="btn btn-success"
                    onclick="app.__vue__.$modal.show('kanban-entry-modal',  {'kanban_id': {{ $kanban->id }} });">
                   {{ trans('global.kanbanEntry.create') }}
                </button>
            </div>
        </div>
    @endcan

    <!-- Timelime example  -->
    <div id="kanban_board_wrapper"
         style="position:absolute; width: calc(100vw - 270px);height: calc(100vh - 175px);overflow-x:auto;overflow-y: hidden;">
        <kanban-board  :kanban="{{ $kanban }}"></kanban-board>
    </div>
</div>
<!-- /.timeline -->
<subscribe-modal></subscribe-modal>
<medium-modal></medium-modal>
<medium-create-modal></medium-create-modal>
<task-modal></task-modal>
@endsection

@section('scripts')
@parent

<script>
    $(function() {
        if (localStorage.getItem('menu_toggle_class') === 'sidebar-collapse') {
            $("#kanban_board_wrapper").width($("#kanban_board_wrapper").width()+170);
        }
    });
</script>

@endsection

