@extends('layouts.master')
@section('title')
    {{ trans('global.permission.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.permission.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('permission_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("permissions.create") }}">
                {{ trans('global.permission.create') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="permissions-datatable"
                   class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('global.permission.fields.title') }}</th>
                        <th>{{ trans('global.datatables.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $key => $permission)
                        <tr data-entry-id="{{ $permission->id }}">
                            <td></td>
                            <td>{{ $permission->title ?? '' }}</td>
                            <td>
                                @can('permission_show')
                                    <a class="btn btn-xs btn-success mr-1" href="{{ route('permissions.show', $permission->id) }}">
                                        <i class="fa fa-list-alt"></i>
                                    </a>
                                @endcan
                                @can('permission_edit')
                                    <a class="btn btn-xs btn-primary mr-1" href="{{ route('permissions.edit', $permission->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endcan
                                @can('permission_delete')
                                    <form action="{{ route('permissions.destroy', $permission->id) }}" 
                                          method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" 
                                          class="pull-right">
                                        <input type="hidden" 
                                               name="_method" 
                                               value="DELETE">
                                        <input type="hidden" 
                                               name="_token" 
                                               value="{{ csrf_token() }}">
                                        <button type="submit" 
                                                id="delete-grade-{{ $permission->id }}" 
                                                class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
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
@endsection
@section('scripts')
@parent
<script>
    $(function () {
        let deleteButton = {
            text: '{{ trans('global.datatables.delete') }}',
            url: "{{ route('permissions.massDestroy') }}",
            className: 'btn-danger btn-xs',
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
    @can('permission_delete')
        dtButtons.push(deleteButton)
    @endcan

    var table = $('#permissions-datatable').DataTable({ buttons: dtButtons })
})

</script>
@endsection