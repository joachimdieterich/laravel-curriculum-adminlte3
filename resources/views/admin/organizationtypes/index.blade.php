@extends('layouts.admin')
@section('content')
@can('organization_type_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <button class="btn btn-success" href="{{ route("admin.organizationtypes.create") }}" @click.prevent="$modal.show('organizationtype-modal')">
                {{ trans('global.add') }} {{ trans('global.organizationtype.title_singular') }}
            </button>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.organizationtype.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="laravel_datatable" class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('global.organizationtype.fields.title') }}
                        </th>
                        <th>
                            {{ trans('global.organizationtype.fields.external_id') }}
                        </th>
                        <th>
                            {{ trans('global.country.title') }}
                        </th>
                        <th>
                            {{ trans('global.state.title') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                
 <!--                 <tbody>
                  @foreach($organization_types as $key => $organization_type)
                        <tr data-entry-id="{{ $organization_type->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $organization_type->title ?? '' }}
                            </td>
                            <td>
                                {{ $organization_type->external_id ?? '' }}
                            </td>
                            <td>
                                {{ $organization_type->state->lang_de ?? '' }}
                            </td>
                            <td>
                                {{ $organization_type->country->lang_de ?? '' }}
                            </td>
                            <td>
                                @can('organization_type_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.organizationtypes.show', $organization_type->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('organization_type_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.organizationtypes.edit', $organization_type->id) }}" >
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('organization_type_delete')
                                    <form action="{{ route('admin.organizationtypes.destroy', $organization_type->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>

                        </tr>
                   
                </tbody> @endforeach-->
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
 <script>
   $(document).ready( function () {
    $('#laravel_datatable').DataTable({
           processing: true,
           serverSide: true,
           ajax: "{{ url('admin/organizationTypeList') }}",
           columns: [
                    { data: '' },
                    { data: 'title' },
                    { data: 'external_id' },
                    { data: 'state_id' },
                    { data: 'country_id' },
                 
                 ]
        });
     });
  </script>

<!--<script>
    $(function () {
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.organizationtypes.massDestroy') }}",
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
@can('organization_type_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>-->
@endsection