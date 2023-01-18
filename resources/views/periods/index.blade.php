@extends('layouts.master')
@section('title')
    {{ trans('global.period.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.period.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-period"
               class="btn btn-success"
               href="{{ route("periods.create") }}" >
               {{ trans('global.period.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="periods-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"></th>
            <th>{{ trans('global.period.fields.title') }}</th>
            <th>{{ trans('global.period.fields.begin') }}</th>
            <th>{{ trans('global.period.fields.end') }}</th>
            <th>{{ trans('global.datatables.action') }}</th>
        </tr>
    </thead>
</table>

@endsection
@section('scripts')
@parent
<script>
$(document).ready( function () {
    let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

    var table = $('#periods-datatable').DataTable({
        ajax: "{{ url('periods/list') }}",
        columns: [
                 { data: 'check'},
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
        window.location.href = "/periods/" + table.row(this).id()
    });

});

function getDatatablesIds(selector) {
    return $(selector).DataTable().rows({selected: true}).ids().toArray();
}

function generateProcessList(ids) {
    var processList = [];
    for (i = 0; i < ids.length; i++) {
        processList.push({
            period_id: ids[i],
            curriculum_id: $('#period_curricula').val(),
        });
    }
    return processList;
}

function sendRequest(method, url, ids, data){
    if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')
        return
    }
    if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
            headers: {'x-csrf-token': _token},
            method: method,
            url: url,
            data: data
        })
        .done(function () { location.reload() })
    }
}
</script>
@endsection
