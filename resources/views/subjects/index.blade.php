@extends('layouts.master')
@section('title')
    {{ trans('global.subject.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.subject.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('subject_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-subject"
                class="btn btn-success" href="{{ route("subjects.create") }}">
                {{ trans('global.subject.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="subjects-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"> </th>
            <th>{{ trans('global.subject.fields.title') }}</th>
            <th>{{ trans('global.subject.fields.title_short') }}</th>
            <th>{{ trans('global.datatables.action') }}</th>
        </tr>
    </thead>
</table>

<subject-modal></subject-modal>
@endsection
@section('scripts')
@parent
<script>
    $(function () {

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    var table = $('#subjects-datatable').DataTable({
        ajax: "{{ url('subjects/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'title_short' },
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
        window.location.href = "/subjects/" + table.row({ selected: true }).data().id ;
    });
})

</script>
@endsection
