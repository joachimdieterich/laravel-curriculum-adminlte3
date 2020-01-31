@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ trans('global.logbook.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER')) 
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/">{{ trans('global.home') }}</a>
        @endif
    </li>
    <li class="breadcrumb-item active">{{ trans('global.logbook.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-logbook" 
               class="btn btn-success" 
               href="{{ route("logbooks.create") }}" >
               {{ trans('global.logbook.create') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <table id="logbooks-datatable" class=" table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('global.logbook.fields.title') }}</th>
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
    var table = $('#logbooks-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('logbooks/list') }}",
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