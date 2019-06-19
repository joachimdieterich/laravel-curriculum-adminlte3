@extends('layouts.admin')
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("curricula.create") }}" >
                {{ trans('global.add') }} {{ trans('global.curriculum.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.curriculum.title_singular') }} {{ trans('global.list') }}
    </div>

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
    var table = $('#curricula-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('curriculaList') }}",
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
</script>

@endsection