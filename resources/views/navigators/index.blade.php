@extends('layouts.master')
@section('title')
    {{ trans('global.navigator.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.navigator.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"
                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a id="add-navigator"
                   class="btn btn-success"
                   href="{{ route("navigators.create") }}">
                    {{ trans('global.navigator.create') }}
                </a>
        </div>
    </div>
@endcan
<table id="navigators-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"></th>
            <th>{{ trans('global.navigator.fields.title') }}</th>
            <th>{{ trans('global.organization.title_singular') }}</th>
            <th>{{ trans('global.datatables.action') }}</th>
        </tr>
    </thead>
</table>

@endsection
@section('scripts')
@parent

<script>
$(document).ready( function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    var table = $('#navigators-datatable').DataTable({
        ajax: "{{ url('navigators/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'organization' },
                 { data: 'action' }
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
        window.location.href = "/navigators/" + table.row({ selected: true }).data().id ;
    });
 });
</script>

@endsection
