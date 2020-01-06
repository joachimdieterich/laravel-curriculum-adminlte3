@extends('layouts.master')
@section('title')
    {{ trans('global.role.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.role.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('role_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("roles.create") }}">
                {{ trans('global.role.create') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <table id="roles-datatable" class="table table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ trans('global.organization.fields.title') }}</th>
                    <th>{{ trans('global.datatables.action') }}</th>
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
        ajax: "{{ url('roles/list') }}",
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