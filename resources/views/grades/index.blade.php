@extends('layouts.admin')
@section('content')
@can('grade_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <button class="btn btn-success" href="{{ route("grades.create") }}" @click.prevent="$modal.show('grade-modal')">
                {{ trans('global.add') }} {{ trans('global.grade.title_singular') }}
            </button>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.grade.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('global.grade.fields.title') }}
                        </th>
                        <th>
                            {{ trans('global.grade.fields.external_begin') }}
                        </th>
                        <th>
                            {{ trans('global.grade.fields.external_end') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grades as $key => $grade)
                        <tr data-entry-id="{{ $grade->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $grade->title ?? '' }}
                            </td>
                            <td>
                                {{ $grade->external_begin ?? '' }}
                            </td>
                            <td>
                                {{ $grade->external_end ?? '' }}
                            </td>
                            <td>
                                @can('grade_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('grades.show', $grade->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('grade_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('grades.edit', $grade->id) }}" >
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('grade_delete')
                                    <form action="{{ route('grades.destroy', $grade->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection