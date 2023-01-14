@extends('layouts.master')
@section('title')
    {{ trans('global.metadataset.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.metadataset.title') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @can('organization_type_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("metadatasets.create") }}">
                    {{ trans('global.metadataset.create') }}
                </a>
            </div>
        </div>
@endcan
<table id="metadataset_datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th></th>
            <th>{{ trans('global.metadataset.fields.version') }}</th>
            <th>{{ trans('global.created_at') }}</th>
            <th>{{ trans('global.datatables.action') }}</th>
        </tr>
    </thead>
</table>

@endsection
@section('scripts')
@parent
<script>
   $(document).ready( function () {
   var table = $('#metadataset_datatable').DataTable({
        ajax: "{{ url('metadatasets/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'version' },
                 { data: 'created_at' },
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
        });
        table.on( 'select', function ( e, dt, type, indexes ) { //on select event
            window.location.href = "/metadatasets/" + table.row({ selected: true }).data().id ;
        });

     });
</script>
@endsection
