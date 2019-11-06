@extends('layouts.master')
@section('title')
      {{ trans('global.grade.title_singular') }} {{ trans('global.list') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">Home</a></li>
    <li class="breadcrumb-item active"> {{ trans('global.grade.title_singular') }} {{ trans('global.list') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')
@can('grade_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-grade" 
                    class="btn btn-success" href="{{ route("grades.create") }}">
                {{ trans('global.add') }} {{ trans('global.grade.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <table id="grades-datatable" class="table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th width="10"> </th>
                    <th>{{ trans('global.grade.fields.title') }}</th>
                    <th>{{ trans('global.grade.fields.external_begin') }}</th>
                    <th>{{ trans('global.grade.fields.external_end') }}</th>
                    <th>{{ trans('global.organization.title_singular') }}</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<grade-modal></grade-modal>


@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('grades.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

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
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('grade_delete')
  dtButtons.push(deleteButton)
@endcan

 var table = $('#grades-datatable').DataTable({
        processing: true,
        serverSide: true,
        select: true,
        ajax: "{{ url('grades/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'external_begin' },
                 { data: 'external_end' },
                 { data: 'organization_type' },
                 { data: 'action' }
                ],
        buttons: dtButtons
    });
})

</script>
@endsection