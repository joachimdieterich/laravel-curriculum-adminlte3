@extends('layouts.admin')
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("groups.create") }}" >
                {{ trans('global.add') }} {{ trans('global.group.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.group.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table id="groups-datatable" class=" table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('global.group.fields.title') }}</th>
                    <th>{{ trans('global.grade.title_singular') }}</th>
                    <th>{{ trans('global.period.title_singular') }}</th>
                    <th>{{ trans('global.organization.title_singular') }}</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    
    <div class="row ">
        <div class="col-sm-12">
            <div class="card">  
                <div class="card-header">
                    <ul class="nav nav-pills">
                        <li id="nav_tab_group" class="nav-item">
                            <a href="#tab_group" class="nav-link active" data-toggle="tab">Lehrpläne / Kompetenzraster</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">

                        <!--@can('user_edit')-->
                            <div id="tab_group" class="tab-pane active row" >
                                <div class="form-horizontal col-xs-12">
                                    @include ('forms.input.info', ["value" => "Markierte Gruppen in Lehrpläne / Kompetenzraster ein bzw. ausschreiben."])

                                    @include ('forms.input.select', 
                                        ["model" => "group", 
                                        "show_label" => true,
                                        "field" => "group_curricula",  
                                        "options"=> $curricula, 
                                        "option_label" => "title",    
                                        "value" =>  old('group_id', isset($user->current_group_id) ? $user->current_group_id : '')])     

                                    <div class="btn-group pull-right" role="group" aria-label="...">    
                                        @include ('forms.input.button', ["onclick" => "enroleToCurricula()", "field" => "enroleToCurricula", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-plus", "label" => "In Lehrplan einschreiben"])
                                        @include ('forms.input.button', ["onclick" => "expelFromCurricula()", "field" => "expelFromCurricula", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-minus", "label" => "Aus Lehrplan ausschreiben"])
                                    </div>
                                </div>
                            </div>
                        <!--@endcan-->



                     </div><!-- ./tab-content -->
                </div>
            </div>
        </div><!-- ./col-xs-12 -->  
    </div>
</div>


@endsection
@section('scripts')
@parent

<script>
$(document).ready( function () {
    let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
    let deleteButton = {
      text: deleteButtonTrans,
      url: "{{ route('groups.massDestroy') }}",
      className: 'btn-danger',
      action: function (e, dt, node, config) {
        var ids = dt.rows({ selected: true }).ids().toArray()

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
    @can('group_delete')
      dtButtons.push(deleteButton)
    @endcan
    
    var table = $('#groups-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('groups/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'title' },
                 { data: 'grade' },
                 { data: 'period' },
                 { data: 'organization' },
                 { data: 'action' }
                ],
        buttons: dtButtons
    });
    //align header/body
    $(".dataTables_scrollHeadInner").css({"width":"100%"});
    $(".table ").css({"width":"100%"});
 });
 
function enroleToCurricula()
{
    var ids = getDatatablesIds('#groups-datatable');
    if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')
    }
    if (confirm('{{ trans('global.areYouSure') }}')) {
        var enrolments = [];
        for (i = 0; i < ids.length; i++) { 
            enrolments.push({
                group_id: ids[i],
                curriculum_id: $('#group_curricula').val(),
            });
        }
        sendRequest('POST', '/curricula/enrol', { enrollment_list: enrolments, _method: 'POST'});
    }  
}

function getDatatablesIds(selector){
    return $(selector).DataTable().rows({ selected: true }).ids().toArray();
}
  
function sendRequest(method, url, data){
    $.ajax({
        headers: {'x-csrf-token': _token},
        method: method,
        url: url,
        data: data
    })
    .done(function () { location.reload() })
}  

function expelFromCurricula()
{
    var ids = getDatatablesIds('#groups-datatable');
    if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')
        return
    }

    if (confirm('{{ trans('global.areYouSure') }}')) {
        var expellments = [];
        for (i = 0; i < ids.length; i++) { 
            expellments.push({
                group_id: ids[i],
                curriculum_id: $('#group_curricula').val(),
            });
        }
        sendRequest('POST', '/curricula/expel', { expel_list: expellments, _method: 'DELETE'});
    }  
}
 
</script>


@endsection
