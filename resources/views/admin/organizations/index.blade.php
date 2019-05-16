@extends('layouts.admin')
@section('content')
@can('organization_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <button class="btn btn-success" href="{{ route("admin.organizations.create") }}" @click.prevent="$modal.show('organization-modal')">
                {{ trans('global.add') }} {{ trans('global.organization.title_singular') }}
            </button>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.organization.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('global.organization.fields.title') }}
                        </th>
                        <th>
                            {{ trans('global.organization.fields.description') }}
                        </th>
                        <th>
                            {{ trans('global.organization.fields.street') }}
                        </th>
                        <th>
                            {{ trans('global.organization.fields.postcode') }}
                        </th>
                        <th>
                            {{ trans('global.organization.fields.city') }}
                        </th>
                        <th>
                            {{ trans('global.organization.fields.phone') }}
                        </th>
                        <th>
                            {{ trans('global.organization.fields.email') }}
                        </th>
                        <th>
                            {{ trans('global.organization.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($organizations as $key => $organization)
                        <tr data-entry-id="{{ $organization->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $organization->title ?? '' }}
                            </td>
                            <td>
                                {{ $organization->description ?? '' }}
                            </td>
                            <td>
                                {{ $organization->street ?? '' }}
                            </td>
                            <td>
                                {{ $organization->postcode ?? '' }}
                            </td>
                            <td>
                                {{ $organization->city ?? '' }}
                            </td>
                            <td>
                                {{ $organization->phone ?? '' }}
                            </td>
                            <td>
                                {{ $organization->email ?? '' }}
                            </td>
                            <td>
                                @can('organization_edit')
                                <form action="{{ route('admin.organizations.update', $organization->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                @include ('forms.input.select', 
                                ["model" => "status", 
                                "show_label" => false, 
                                "field" => "status_id",  
                                "options"=> $statuses, 
                                "onchange"=> "this.form.submit()",  
                                "value" => old('status_id', isset($organization->status_id) ? $organization->status_id : '') ])
                                </form>
                                    
                                @endcan
                                
                            </td>
                            <td>
                                @can('organization_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.organizations.show', $organization->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('organization_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.organizations.edit', $organization->id) }}" >
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('organization_delete')
                                    <form action="{{ route('admin.organizations.destroy', $organization->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
<organization-modal></organization-modal>


@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.organizations.massDestroy') }}",
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
@can('organization_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection