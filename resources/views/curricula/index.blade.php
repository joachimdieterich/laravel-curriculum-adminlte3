@extends('layouts.master')
@section('title')
    {{ trans('global.curriculum.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"
                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @can('curriculum_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("curricula.create") }}">
                    {{ trans('global.curriculum.create') }}
                </a>
                <a class="btn btn-success" href="{{ route("curricula.import") }}">
                    {{ trans('global.curriculum.import') }}
            </a>
        </div>
    </div>
@endcan

<table id="curricula-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"></th>
            <th>{{ trans('global.curriculum.fields.title') }}</th>
            <th>{{ trans('global.grade.title_singular') }}</th>
            <th>{{ trans('global.subject.title_singular') }}</th>
            <th>{{ trans('global.organizationtype.title_singular') }}</th>
            <th>{{ trans('global.curriculumtype.title') }}</th>
            <th>{{ trans('global.owner') }}</th>
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
    var table = $('#curricula-datatable').DataTable({
        ajax: "{{ url('curricula/list') }}",

        columns: [
            { data: 'check'},
            { data: 'title' },
            { data: 'grade' },
            { data: 'subject' },
            { data: 'organizationtype' },
            { data: 'type' },
            { data: 'owner' },
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
        /*bStateSave: true,
        fnStateSave: function (oSettings, oData) {
            localStorage.setItem( 'DataTables', JSON.stringify(oData) );
        },
        fnStateLoad: function (oSettings) {
            return JSON.parse( localStorage.getItem('DataTables') );
        },*/
    });
    //table.rows().deselect();
    table.on('select', function (e, dt, type, indexes) { //on select event
        window.location.href = "/curricula/" + table.row({selected: true}).data().id;
    });


});

</script>

@endsection
