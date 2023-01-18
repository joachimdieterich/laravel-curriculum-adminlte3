@extends('layouts.master')
@section('title')
    {{ trans('global.meeting.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.meeting.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('plan_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a
                id="add-plan"
                class="btn btn-success"
                href="{{ route("meetings.create") }}">
                {{ trans('global.meeting.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="meetings-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th></th>
            <th>{{ trans('global.meeting.fields.uid') }}</th>
            <th>{{ trans('global.meeting.fields.title') }}</th>
            <th>{{ trans('global.meeting.fields.begin') }}</th>
            <th>{{ trans('global.meeting.fields.end') }}</th>
            <th>{{ trans('global.datatables.action') }}</th>
        </tr>
    </thead>
</table>

@endsection
@section('scripts')
@parent
<script>
$(document).ready( function () {

    let dtButtons = '';
    var table = $('#meetings-datatable').DataTable({
        ajax: "{{ url('meetings/list') }}",
        columns: [
             { data: 'check'},
             { data: 'uid' },
             { data: 'title' },
             { data: 'begin' },
             { data: 'end' },
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
        window.location.href = "/meetings/" + table.row(this).id()
    });

});
</script>

@endsection
