@extends('layouts.master')
@section('title')
    {{ trans('global.add') }} {{ trans('global.role.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.add') }} {{ trans('global.role.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')
@can('role_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("roles.create") }}">
                {{ trans('global.add') }} {{ trans('global.role.title_singular') }}
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