@extends((Auth::user()->id == env('GUEST_USER')) || $is_shared ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    @can('kanban_create')
            <color-picker-component
                id="{{ $kanban->id }}"
                class="@if(!$may_edit) d-none @endif"
            ></color-picker-component>
    @endcan
    <small>{{ $kanban->title }} </small>

    <span></span>
    @if(Auth::user()->id == $kanban->owner_id)
        <a class="btn btn-flat"
           href="/kanbans/{{ $kanban->id }}/edit">
            <i class="fa fa-pencil-alt text-secondary"></i>
        </a>
        @can('kanban_create')
            @if(!$is_shared)
                <button class="btn btn-flat"
                        onclick="app.__vue__.$modal.show('subscribe-modal',  {'modelId': {{ $kanban->id }}, 'modelUrl': 'kanban','shareWithToken': true });">
                    <i class="fa fa-share-alt text-secondary"></i>
                </button>


            @endif
        @endcan
        <a href="/export_csv/{{$kanban->id}}" class="btn p-0">
            <i class="fa fa-file-csv text-secondary"></i>
        </a>

        <a href="/export_pdf/{{$kanban->id}}" class="btn p-0">
            <i class="fa fa-file-pdf text-secondary"></i>
        </a>
    @endif
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/"><i class="fa fa-home"></i></a>
        @endif
    </li>
    <li class="breadcrumb-item"><a href="{{ route("kanbans.index") }}">{{ trans('global.kanban.title') }}</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($kanban->title, 10) }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"
                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection

@section('content')
    <!-- {!! $kanban->description !!}-->
    <div>
        <kanban-board
            :editable="{{ $may_edit ? "1":"0" }}"
            :pusher="{{ $is_pusher_active ? "1":"0" }}"
            ref="kanbanBoard"
            :kanban="{{ $kanban }}"></kanban-board>
    </div>

    <subscribe-modal></subscribe-modal>
    <medium-modal></medium-modal>
    <medium-create-modal></medium-create-modal>
    <task-modal></task-modal>
@endsection

@section('scripts')
    @parent

    <script>
        $(function () {
            if (localStorage.getItem('menu_toggle_class') === 'sidebar-collapse') {
                const wrapper = $("#kanban_board_wrapper");
                wrapper.width(wrapper.width() + 170);
            }
        });
    </script>

@endsection
