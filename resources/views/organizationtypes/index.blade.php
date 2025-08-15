@extends('layouts.master')
@section('title')
    {{ trans('global.organizationtype.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.organizationtype.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('organization_type_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("organizationtypes.create") }}" >
                {{ trans('global.organizationtype.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="organization_type_datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th></th>
            <th>{{ trans('global.organizationtype.fields.title') }}</th>
            <th>{{ trans('global.organizationtype.fields.external_id') }}</th>
            <th>{{ trans('global.country.title') }}</th>
            <th>{{ trans('global.state.title') }}</th>
            <th>{{ trans('global.datatables.action') }}</th>
        </tr>
    </thead>
</table>

@endsection
@section('scripts')
@parent
<script>
   $(document).ready( function () {
   var table = $('#organization_type_datatable').DataTable({
        ajax: "{{ url('organizationtypes/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'external_id' },
                 { data: 'state_id' },
                 { data: 'country_id' },
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
   });
       table.on('click', 'tr', function () {
           window.location.href = "/organizationtypes/" + table.row(this).id()
       });

   });
</script>
@endsection
