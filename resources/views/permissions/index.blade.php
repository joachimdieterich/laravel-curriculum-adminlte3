@extends('layouts.master')
@section('title')
    {{ trans('global.permission.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.permission.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('permission_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("permissions.create") }}">
                {{ trans('global.permission.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="users-datatable"
       class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"></th>
            <th>{{ trans('global.permission.fields.title') }}</th>
            <th></th>
        </tr>
    </thead>
</table>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
        var table = $('#users-datatable').DataTable({
            ajax: "{{ url('permissions/list') }}",
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
            window.location.href = "/permissions/" + table.row(this).id()
        });

    });


</script>
@endsection
