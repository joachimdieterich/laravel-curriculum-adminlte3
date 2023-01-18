@extends((Auth::user()->id == env('GUEST_USER')) || $is_shared ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    @can('kanban_create')
        @if(!$is_shared)
            <color-picker-component id="{{ $kanban->id }}"></color-picker-component>
        @endif
    @endcan
    <small>{{ $kanban->title }} </small>
    @if(Auth::user()->id == $kanban->owner_id)
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
        {{--@can('kanban_entry_create')
            <div style="margin-bottom: 10px;" class="row">
                <div class="col-lg-12">
                    <button id="add-kanban-entry"
                            class="btn btn-success"
                            onclick="app.__vue__.$modal.show('kanban-entry-modal',  {'kanban_id': {{ $kanban->id }} });">
                        {{ trans('global.kanbanEntry.create') }}
                    </button>
                </div>
            </div>
        @endcan--}}

        <!-- Timelime example  -->
        <div id="kanban_board_wrapper"
             style="position:absolute; width: calc(100vw {{ ( $is_shared ? '' : '- 270px') }} - 2rem);height: calc(100vh - 175px - 2rem);overflow:auto; padding: 2rem; background-color: {{ $kanban->background }}">

            <kanban-board
                :editable="{{ $may_edit ? "1":"0" }}"
                ref="kanbanBoard"
                :kanban="{{ $kanban }}"></kanban-board>
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
        $(function () {
            if (localStorage.getItem('menu_toggle_class') === 'sidebar-collapse') {
                $("#kanban_board_wrapper").width($("#kanban_board_wrapper").width() + 170);
            }
        });
    </script>

@endsection
