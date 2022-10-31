@extends('layouts.master')
@section('title')
    {{ trans('global.variantDefinitions.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.variantDefinitions.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('curriculum_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-subject"
                class="btn btn-success" href="{{ route("variantDefinitions.create") }}">
                {{ trans('global.variantDefinitions.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="variant_definitions-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"> </th>
            <th>{{ trans('global.variantDefinitions.fields.title') }}</th>
            <th>{{ trans('global.variantDefinitions.fields.color') }}</th>
            <th>{{ trans('global.variantDefinitions.fields.css_icon') }}</th>
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
    var table = $('#variant_definitions-datatable').DataTable({
        ajax: "{{ url('variantDefinitions/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'color' },
                 { data: 'css_icon' },
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
        bStateSave: false,
        fnStateSave: function (oSettings, oData) {
            localStorage.setItem( 'DataTables', JSON.stringify(oData) );
        },
        fnStateLoad: function (oSettings) {
            return JSON.parse( localStorage.getItem('DataTables') );
        },
        buttons: dtButtons
    });
    table.on( 'select', function ( e, dt, type, indexes ) { //on select event
        window.location.href = "/variantDefinitions/" + table.row({ selected: true }).data().id ;
    });
})

</script>
@endsection
