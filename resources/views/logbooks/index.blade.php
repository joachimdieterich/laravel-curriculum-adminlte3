@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ trans('global.logbook.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/"><i class="fa fa-home"></i></a>
        @endif
    </li>
    <li class="breadcrumb-item active">{{ trans('global.logbook.title') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @can('logbook_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a id="add-logbook"
                   class="btn btn-success"
                   href="{{ route("logbooks.create") }}">
                    {{ trans('global.logbook.create') }}
                </a>
        </div>
    </div>
@endcan

<table id="logbooks-datatable" class="table table-hover datatable">
    <thead>
    <tr>
        <th width="10"></th>
        <th>{{ trans('global.logbook.fields.title') }}</th>
        <th>{{ trans('global.datatables.action') }}</th>
    </tr>
    </thead>
</table>
{{--<data-table-widgets model-url="logbooks"></data-table-widgets>--}}
@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
            var table = $('#logbooks-datatable').DataTable({
                ajax: "{{ url('logbooks/list') }}",
                columns: [
                    { data: 'check'},
                    { data: 'title' },
                    {data: 'action'}
                ],
                columnDefs: [
                    {"visible": false, "targets": 0},
                    {
                        orderable: false,
                        searchable: false,
                        targets: -1
                    }
                ],
                select: false,
                bStateSave: true,
                fnStateSave: function (oSettings, oData) {
                    localStorage.setItem('DataTables', JSON.stringify(oData));
                },
                fnStateLoad: function (oSettings) {
                    return JSON.parse(localStorage.getItem('DataTables'));
                },
                buttons: dtButtons
            });
            table.on('click', 'tr', function () {
                window.location.href = "/logbooks/" + table.row(this).id()
            });

        })

    </script>
@endsection
