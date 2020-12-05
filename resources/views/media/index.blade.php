@extends('layouts.master')
@section('title')
    {{ trans('global.media.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.media.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('medium_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a
                id="add-medium"
                class="btn btn-success"
                onclick="app.__vue__.$modal.show('medium-create-modal',  {'description': {{ json_encode('') }} });">
                {{ trans('global.media.create') }}
            </a>
        </div>
        <div class="col-lg-12">
            @include ('forms.input.file',
            ["model" => "media",
            "field" => "path",
            "label" => false,
            "value" => old('path', isset($media->path) ? $media->id : '')])

        </div>
    </div>
@endcan
<table id="media-datatable" class="table table-hover datatable">
    <thead>
        <tr>
            <th></th>
            <th>{{ trans('global.media.fields.title') }}</th>
            <th>{{ trans('global.media.fields.size') }}</th>
            <th>{{ trans('global.media.fields.created_at') }}</th>
            <th>{{ trans('global.media.fields.public') }}</th>
            <th>{{ trans('global.datatables.action') }}</th>
        </tr>
    </thead>
</table>


<medium-modal></medium-modal>
<medium-create-modal></medium-create-modal>


@endsection
@section('scripts')
@parent
<script>
$(document).ready( function () {
    let dtButtons = '';
    @can('medium_delete')
        let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
        let deleteButton = {
          text: deleteButtonTrans,
          url: "{{ route('media.massDestroy') }}",
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


    var table = $('#media-datatable').DataTable({
        ajax: "{{ url('media/list') }}",
        columns: [
            { data: 'check'},
            { data: 'title' },
            { data: 'size' },
            { data: 'created_at' },
            { data: 'public' },
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
        buttons: dtButtons
    });
    table.on( 'select', function ( e, dt, type, indexes ) { //on select event
        window.location.href = "/media/" + table.row({ selected: true }).data().id ;
    });
 });
</script>

@endsection
