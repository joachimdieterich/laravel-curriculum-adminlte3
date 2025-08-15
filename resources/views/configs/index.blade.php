@extends('layouts.master')
@section('title')
    {{ trans('global.config.title') }}

@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.config.title') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    @if(auth()->user()->role()->id == 1)
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a
                    id="add-config"
                    class="btn btn-success"
                    href="{{ route("configs.create") }}">
                {{ trans('global.config.create') }}
            </a>
        </div>
    </div>
@endif
<div class="row">
    <div class="col-sm-6 col-12">
        <div class="info-box">

            <span class="info-box-icon bg-info">
               <a href="/configs/models" class="link-muted">
                   <i class="fa fa-tachometer-alt"></i>
               </a>
            </span>
            <div class="info-box-content">
                <a href="/configs/models" class="link-muted">
                    <span class="info-box-text">{{ trans('global.config.model_limiter_title') }}</span>
                    <small>
                        <span class="">{{ trans('global.config.model_limiter_description')  }}</span>
                    </small>
                </a>
            </div>

            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>


<table id="configs-datatable" class="table table-hover datatable">
    <thead>
    <tr>
        <th></th>
        <th>{{ trans('global.config.fields.key') }}</th>
        <th>{{ trans('global.config.fields.value') }}</th>
        <th>{{ trans('global.config.fields.referenceable_type') }}</th>
        <th>{{ trans('global.config.fields.referenceable_id') }}</th>
            <th>{{ trans('global.config.fields.data_type') }}</th>
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
    @can('config_delete')
        let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
        let deleteButton = {
          text: deleteButtonTrans,
          url: "{{ route('configs.massDestroy') }}",
          className: 'btn-danger',
          action: function (e, dt, node, config) {
            var ids = dt.rows({ selected: true }).ids().toArray()

            if (ids.length === 0) {
              alert('{{ trans('global.datatables.zero_selected') }}')
              return
            }

            if (confirm('{{ trans('global.areYouSure') }}')) {
              $.ajax({
                headers: {'x-csrf-token': _token},
                method: 'POST',
                url: config.url,
                data: { ids: ids, _method: 'DELETE' }})
                .done(function () { location.reload() })
            }
          }
        }
        dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        dtButtons.push(deleteButton)
    @endcan


    var table = $('#configs-datatable').DataTable({
        ajax: "{{ url('configs/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'key' },
                 { data: 'value' },
                 { data: 'referenceable_type' },
                 { data: 'referenceable_id' },
                 { data: 'data_type' },
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

 });
</script>

@endsection
