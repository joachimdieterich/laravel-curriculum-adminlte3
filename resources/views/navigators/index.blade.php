@extends('layouts.master')
@section('title')
    {{ trans('global.navigator.title_singular') }} {{ trans('global.list') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.navigator.title_singular') }} {{ trans('global.list') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("navigators.create") }}" >
                {{ trans('global.add') }} {{ trans('global.navigator.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <table id="navigators-datatable" class=" table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('global.navigator.fields.title') }}</th>
                    <th>{{ trans('global.organization.title_singular') }}</th>
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
    var table = $('#navigators-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('navigators/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'organization' },
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