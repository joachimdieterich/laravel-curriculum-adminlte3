@extends('layouts.master')
@section('title')
    {{ trans('global.period.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.period.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-period"
               class="btn btn-success" 
               href="{{ route("periods.create") }}" >
               {{ trans('global.period.create') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <table id="periods-datatable" class="table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('global.period.fields.title') }}</th>
                    <th>{{ trans('global.period.fields.begin') }}</th>
                    <th>{{ trans('global.period.fields.end') }}</th>
                    <th>{{ trans('global.organization.title_singular') }}</th>
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
    let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
    
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    
    var table = $('#periods-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('periods/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'begin' },
                 { data: 'end' },
                 { data: 'organization' },
                 { data: 'action' }
                ],
        buttons: dtButtons
    });
    //align header/body
    $(".dataTables_scrollHeadInner").css({"width":"100%"});
    $(".table ").css({"width":"100%"});
 });
 
function getDatatablesIds(selector){
    return $(selector).DataTable().rows({ selected: true }).ids().toArray();
}

function generateProcessList(ids){
    var processList = [];
    for (i = 0; i < ids.length; i++) { 
        processList.push({
            period_id: ids[i],
            curriculum_id: $('#period_curricula').val(),
        });
    }
    return processList;
}
  
function sendRequest(method, url, ids, data){
    if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')
        return
    }
    if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
            headers: {'x-csrf-token': _token},
            method: method,
            url: url,
            data: data
        })
        .done(function () { location.reload() })
    }
}  
</script>
@endsection