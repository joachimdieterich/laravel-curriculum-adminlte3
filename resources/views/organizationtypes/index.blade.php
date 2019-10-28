@extends('layouts.master')
@section('title')
    {{ trans('global.organizationtype.title_singular') }} {{ trans('global.list') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.organizationtype.title_singular') }} {{ trans('global.list') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')
@can('organization_type_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <button class="btn btn-success" href="{{ route("organizationtypes.create") }}" @click.prevent="$modal.show('organizationtype-modal')">
                {{ trans('global.add') }} {{ trans('global.organizationtype.title_singular') }}
            </button>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
        <table id="organization_type_datatable" class="table table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ trans('global.organizationtype.fields.title') }}</th>
                    <th>{{ trans('global.organizationtype.fields.external_id') }}</th>
                    <th>{{ trans('global.country.title') }}</th>
                    <th>{{ trans('global.state.title') }}</th>
                    <!--<th>Action</th>-->
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
    $('#organization_type_datatable').DataTable({
           processing: true,
           serverSide: true,
           ajax: "{{ url('organizationTypeList') }}",
           columns: [
                    { data: 'check'},
                    { data: 'title' },
                    { data: 'external_id' },
                    { data: 'state_id' },
                    { data: 'country_id' },
                    //{ data: 'action' }
                 ]
        });
     });
      //align header/body
    $(".dataTables_scrollHeadInner").css({"width":"100%"});
    $(".table ").css({"width":"100%"});
  </script>
@endsection