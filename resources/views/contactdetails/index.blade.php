@extends('layouts.master')
@section('title')
    {{ trans('global.contactdetail.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.contactdetail.title') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @can('contactdetail_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a
                    id="add-contactdetail"
                    class="btn btn-success"
                    href="{{ route("contactdetails.create") }}">
                    {{ trans('global.contactdetail.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="contactdetails-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th></th>
            <th>{{ trans('global.contactdetail.fields.title') }}</th>
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
    var table = $('#contactdetails-datatable').DataTable({
        ajax: "{{ url('contactdetails/list') }}",
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
        window.location.href = "/contactdetails/" + table.row({ selected: true }).data().id ;
    });
 });
</script>

@endsection
