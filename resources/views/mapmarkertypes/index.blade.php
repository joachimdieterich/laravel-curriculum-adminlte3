@extends('layouts.master')
@section('title')
    {{ trans('global.mapMarkerTypes.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.mapMarkerTypes.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('map_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-objectivetype"
                class="btn btn-success" href="{{ route("mapMarkerTypes.create") }}">
                {{ trans('global.mapMarkerType.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="mapmarkertypes-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"> </th>
            <th>{{ trans('global.mapMarkerType.fields.title') }}</th>
            <th>{{ trans('global.datatables.action') }}</th>
        </tr>
    </thead>
</table>

@endsection
@section('scripts')
@parent
<script>
    $(function () {

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    var table = $('#mapmarkertypes-datatable').DataTable({
        ajax: "{{ url('mapMarkerTypes/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'action' }
                ],
        columnDefs: [
            { "visible": false, "targets": 0 },
            {
                orderable: false,
                searchable: false,
                targets: - 1
            }
        ],
        bStateSave: true,
        fnStateSave: function (oSettings, oData) {
            localStorage.setItem( 'DataTables', JSON.stringify(oData) );
        },
        fnStateLoad: function (oSettings) {
            return JSON.parse( localStorage.getItem('DataTables') );
        },
        buttons: dtButtons
    });
    table.on( 'select', function ( e, dt, type, indexes ) { //on select event
        window.location.href = "/mapMarkerTypes/" + table.row({ selected: true }).data().id ;
    });
})

</script>
@endsection
