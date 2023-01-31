@extends('layouts.master')
@section('title')
    {{ trans('global.videoconference.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.videoconference.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('videoconference_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-videoconference"
                class="btn btn-success" href="{{ route("videoconferences.create") }}">
                {{ trans('global.videoconference.create') }}
            </a>
        </div>
    </div>
@endcan
<table id="videoconferences-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"> </th>
            <th>{{ trans('global.videoconference.fields.meetingName') }}</th>
            <th>{{ trans('global.videoconference.fields.attendeePW') }}</th>
            <th>{{ trans('global.videoconference.fields.moderatorPW') }}</th>
            <th>{{ trans('global.videoconference.fields.callbackURL') }}</th>
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
    var table = $('#videoconferences-datatable').DataTable({
        ajax: "{{ url('videoconferences/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'meetingName' },
                 { data: 'attendeePW' },
                 { data: 'moderatorPW' },
                 { data: 'callbackURL' },
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
        window.location.href = "/videoconferences/" + table.row({ selected: true }).data().id ;
    });
})

</script>
@endsection
