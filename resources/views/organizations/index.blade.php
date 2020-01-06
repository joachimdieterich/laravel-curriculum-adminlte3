@extends('layouts.master')
@section('title')
    {{ trans('global.organization.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.organization.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('organization_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <button id="add-organization"
                    class="btn btn-success" 
                    href="{{ route("organizations.create") }}" 
                    @click.prevent="$modal.show('organization-modal', {'method': 'post'})">
                {{ trans('global.organization.create') }}
            </button>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <table id="organizations-datatable" class="table table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ trans('global.organization.fields.title') }}</th>
                    <th>{{ trans('global.organization.fields.street') }}</th>
                    <th>{{ trans('global.organization.fields.postcode') }}</th>
                    <th>{{ trans('global.organization.fields.city') }}</th>
                    <th>{{ trans('global.organization.fields.status') }}</th>
                    <th>{{ trans('global.datatables.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<organization-modal></organization-modal>


@endsection
@section('scripts')
@parent
<script>
$(document).ready( function () {
    let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
    let deleteButton = {
      text: deleteButtonTrans,
      url: "{{ route('organizations.massDestroy') }}",
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
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    @can('organization_delete')
      dtButtons.push(deleteButton)
    @endcan
    
    
    $('#organizations-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('organizations/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'street' },
                 { data: 'postcode' },
                 { data: 'city' },
                 { data: 'status' },
                 { data: 'action' }
                ],
        buttons: dtButtons
    });
    //align header/body
    $(".dataTables_scrollHeadInner").css({"width":"100%"});
    $(".table ").css({"width":"100%"});
 });
</script>

@endsection