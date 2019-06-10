@extends('layouts.admin')
@section('content')
@can('role_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.roles.create") }}">
                {{ trans('global.add') }} {{ trans('global.role.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.role.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table id="roles-datatable" class="table table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ trans('global.organization.fields.title') }}</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    
</div>
@endsection

@section('scripts')
@parent
<script>
$(document).ready( function () {

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    
    $('#roles-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin/roles/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
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