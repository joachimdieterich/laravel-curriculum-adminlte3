@extends('layouts.master')
@section('title')
    {{ trans('global.role.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.role.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('role_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("roles.create") }}">
                {{ trans('global.role.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="roles-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th></th>
            <th>{{ trans('global.organization.fields.title') }}</th>
            <th></th>
        </tr>
    </thead>
</table>
@endsection

@section('scripts')
@parent
<script>
$(document).ready( function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

    var table = $('#roles-datatable').DataTable({
        ajax: "{{ url('roles/list') }}",
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
        window.location.href = "/roles/" + table.row(this).id()
    });

});
</script>
@endsection
