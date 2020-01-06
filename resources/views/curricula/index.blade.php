@extends('layouts.master')
@section('title')
     {{ trans('global.curriculum.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("curricula.create") }}" >
                {{ trans('global.curriculum.create') }}
            </a>
            <a class="btn btn-success" href="{{ route("curricula.import") }}" >
                {{ trans('global.curriculum.import') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <table id="curricula-datatable" class=" table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('global.curriculum.fields.title') }}</th>
                    <th>{{ trans('global.state.title_singular') }}</th>
                    <th>{{ trans('global.country.title_singular') }}</th>
                    <th>{{ trans('global.grade.title_singular') }}</th>
                    <th>{{ trans('global.subject.title_singular') }}</th>
                    <th>{{ trans('global.user.title_singular') }}</th>
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
    var table = $('#curricula-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('curricula/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'state' },
                 { data: 'country' },
                 { data: 'grade' },
                 { data: 'subject' },
                 { data: 'owner' },
                 { data: 'action' }
                ],
        buttons: dtButtons
    });
    //align header/body
    $(".dataTables_scrollHeadInner").css({"width":"100%"});
    $(".table ").css({"width":"100%"});
    
    
 });
 
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

function  destroyCurriculum(id) {
    sendRequest('POST', "curricula/"+id, id, { _method: 'DELETE' });   
} 
</script>

@endsection