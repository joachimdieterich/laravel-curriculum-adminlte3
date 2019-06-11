@extends('layouts.admin')
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.users.create") }}">
                {{ trans('global.add') }} {{ trans('global.user.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.user.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table id="users-datatable" class=" table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('global.user.fields.username') }}</th>
                    <th>{{ trans('global.user.fields.firstname') }}</th>
                    <th>{{ trans('global.user.fields.lastname') }}</th>
                    <th>{{ trans('global.user.fields.email') }}</th>
                    <th>{{ trans('global.user.fields.email_verified_at') }}</th>
                    <th>{{ trans('global.status.title_singular') }}</th>
                    <th>Action</th>
                </tr>
            </thead>     
        </table>
    </div>
</div>

<div class="row ">
    <div class="col-sm-12">
        <div class="card">  
            <div class="card-header">
                <ul class="nav nav-pills">
                    @can('user_reset_password')
                        <li id="nav_tab_password" class="nav-item">
                            <a href="#tab_password" class="nav-link" data-toggle="tab">Passwort</a>
                        </li>
                    @endcan
                    <!--@canany(['user_enroleToGroup', 'user_expelFromGroup'])-->
                    <!--@endcanany-->
                        <li id="nav_tab_group" class="nav-item">
                            <a href="#tab_group" class="nav-link" data-toggle="tab">Lerngruppe</a>
                        </li>

                    <!--@can('user_updateRole')-->
                    <!--@endcan-->
                        <li id="nav_tab_organization" class="nav-item">
                            <a href="#tab_organization" class="nav-link" data-toggle="tab">Institution / Rolle</a>
                        </li>

                    <!--@can('user_userListComplete')-->
                    <!--@endcan-->
                        <li id="nav_tab_register" class="nav-item">
                            <a href="#tab_register" class="nav-link" data-toggle="tab">Registerung bestätigen</a>
                        </li>

                    <!--@can('user_delete')-->
                    <!--@endcan-->
                        <li id="nav_tab_delete" class="nav-item">
                            <a href="#tab_delete" class="nav-link" data-toggle="tab"><span class="text">löschen</span></a>
                        </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    @can('user_reset_password')
                        <div id="tab_password" class="tab-pane active row " >
                            <div class="form-horizontal col-xs-12">
                            @include ('forms.input.info', ["value" => "Neues Passwort für markierte Benutzer festlegen. Passwort muss mind. 6 Zeichen lang sein."])
                            @include ('forms.input.password', ["model" => "user", "field" => "password", "placeholder" => "New Password", "type" => "password", "value" => ""])
                            @include ('forms.input.checkbox', ["field" => "login_password_confirmation", "value" => ""])
                            @include ('forms.input.button', ["onclick" => "resetPassword()", "field" => "confirmed", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-lock", "label" => "Passwort zurücksetzen"])
                            </div>
                        </div>
                    @endcan
                    
                    @can('user_edit')
                        <div id="tab_group" class="tab-pane row " >
                            <div class="form-horizontal col-xs-12">
                                @include ('forms.input.info', ["value" => "Markierte Benutzer in Lerngruppe ein bzw. ausschreiben.\nBenutzer muss an der entsprechenden Institution eingeschrieben sein, damit  die Lerngruppe angezeigt wird."])
                                    
                                @include ('forms.input.select', 
                                    ["model" => "group", 
                                    "show_label" => true,
                                    "field" => "user_organization_group_id",  
                                    "options"=> $groups, 
                                    "option_label" => "title",    
                                    "optgroup" => $organizations,    
                                    "optgroup_field" => "organization_id",    
                                    "value" =>  old('group_id', isset($user->current_group_id) ? $user->current_group_id : '')])     
                                    
                                <div class="btn-group pull-right" role="group" aria-label="...">    
                                    @include ('forms.input.button', ["onclick" => "enroleToGroup()", "field" => "enroleToGroup", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-plus", "label" => "In Gruppe einschreiben"])
                                    @include ('forms.input.button', ["onclick" => "expelFromGroup()", "field" => "expelFromGroup", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-minus", "label" => "Aus Gruppe ausschreiben"])
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('user_edit')
                        <div id="tab_organization" class="tab-pane row " >
                            <div class="form-horizontal col-xs-12">
                                @include ('forms.input.info', ["value" => "Beim Zuweisen einer Rolle werden die markierten Nutzer automatisch in die aktuelle/ausgewählte Institution eingeschrieben bzw. die Daten aktualisiert."])
                                @include ('forms.input.select', 
                                    ["model" => "organization", 
                                    "show_label" => true,
                                    "field" => "role_organization_id",  
                                    "options"=> $organizations, 
                                    "option_label" => "title",  
                                    "onchange"=> "this.form.submit()",  
                                    "value" =>  old('organization_id', isset($user->current_organization_id) ? $user->current_organization_id : '')]) 
                                 @include ('forms.input.select', 
                                    ["model" => "role", 
                                    "show_label" => true,
                                    "field" => "role_id",  
                                    "options"=> $roles, 
                                    "option_label" => "title",  
                                    "onchange"=> "this.form.submit()",  
                                    "value" =>  old('role_id', isset($user->current_role_id) ? $user->current_role_id : '')])     
                                <div class="btn-group pull-right" role="group" aria-label="...">    
                                    @include ('forms.input.button', ["onclick" => "enroleToOrganization()", "field" => "enroleToOrganization", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-plus", "label" => "Rolle zuweisen / einschreiben"])
                                    @include ('forms.input.button', ["onclick" => "expelFromOrganization()", "field" => "expelFromOrganization", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-minus", "label" => "Rolle entziehen /ausschreiben"])
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('user_edit')
                        <div id="tab_register" class="tab-pane row " >
                            <div class="form-horizontal col-xs-12">
                                 @include ('forms.input.select', 
                                ["model" => "status", 
                                "show_label" => true,
                                "field" => "status_id",  
                                "options"=> $statuses, 
                                "option_id" => "status_id",  
                                "option_label" => "lang_de",
                                "onchange"=> "this.form.submit()",  
                                "value" => old('status_id', isset($user->status_id) ? $user->status_id : '') ])
                                @include ('forms.input.button', ["onclick" => "setStatus()", "field" => "acceptUser", "type" => "submit", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-lock", "label" => "Benutzer bestätigen"])
                            </div>
                        </div>
                    @endcan
                    @can('user_delete')
                        <div id="tab_delete" class="tab-pane row" >
                            <div class="form-horizontal col-xs-12">
                                @include ('forms.input.button', ["onclick" => "massDestroyUser()", "field" => "deleteUser", "type" => "button", "class" => "btn btn-danger pull-right mt-3", "icon" => "fa fa-trash", "label" => "Markierte Benutzer löschen"])
                            </div>
                        </div>
                    @endcan

                    
                 </div><!-- ./tab-content -->
            </div>
        </div>
    </div><!-- ./col-xs-12 -->  
</div>

@endsection
@section('scripts')
@parent

<script>
    
    function enroleToGroup()
    {
        var ids = $('#users-datatable').DataTable().rows({ selected: true }).ids().toArray()
        if (ids.length === 0) {
            alert('{{ trans('global.datatables.zero_selected') }}')
            return
        }

        if (confirm('{{ trans('global.areYouSure') }}')) {
            $.ajax({
              headers: {'x-csrf-token': _token},
              method: 'POST',
              url: "/admin/users/group/massEnrol",
              data: { 
                  ids: ids, 
                  group_id: $('#user_organization_group_id').val(),
                  _method: 'PATCH',
              }
            })
              .done(function () { location.reload() })
        }  
    }
    
    function expelFromGroup()
    {
        var ids = $('#users-datatable').DataTable().rows({ selected: true }).ids().toArray()
        if (ids.length === 0) {
            alert('{{ trans('global.datatables.zero_selected') }}')
            return
        }

        if (confirm('{{ trans('global.areYouSure') }}')) {
            $.ajax({
              headers: {'x-csrf-token': _token},
              method: 'POST',
              url: "/admin/users/group/massExpel",
              data: { 
                  ids: ids, 
                  group_id: $('#user_organization_group_id').val(),
                  _method: 'PATCH', 
              }
            })
              .done(function () { location.reload() })
        }  
    }
    
    function enroleToOrganization()
    {
        var ids = $('#users-datatable').DataTable().rows({ selected: true }).ids().toArray()
        if (ids.length === 0) {
            alert('{{ trans('global.datatables.zero_selected') }}')
            return
        }

        if (confirm('{{ trans('global.areYouSure') }}')) {
            var enrolments = [];
            for (i = 0; i < ids.length; i++) { 
                enrolments.push({
                    user_id: ids[i],
                    organization_id: $('#role_organization_id').val(),
                    role_id: $('#role_id').val()
                });
            }
            $.ajax({
              headers: {'x-csrf-token': _token},
              method: 'POST',
              url: "/admin/organizations/enrol",
              data: { 
                  enrollment_list: enrolments, 
                  _method: 'POST',
              }
            })
              .done(function () { location.reload() })
        }  
    }
    function expelFromOrganization()
    {
        var ids = $('#users-datatable').DataTable().rows({ selected: true }).ids().toArray()
        if (ids.length === 0) {
            alert('{{ trans('global.datatables.zero_selected') }}')
            return
        }

        if (confirm('{{ trans('global.areYouSure') }}')) {
            var expellments = [];
            for (i = 0; i < ids.length; i++) { 
                expellments.push({
                    user_id: ids[i],
                    organization_id: $('#role_organization_id').val()
                });
            }
            $.ajax({
              headers: {'x-csrf-token': _token},
              method: 'POST',
              url: "/admin/organizations/expel",
              data: { 
                  expel_list: expellments, 
                  _method: 'DELETE'
              }
            })
              .done(function () { location.reload() })
        }  
    }
    function setStatus()
    {
        var ids = $('#users-datatable').DataTable().rows({ selected: true }).ids().toArray()
        if (ids.length === 0) {
            alert('{{ trans('global.datatables.zero_selected') }}')
            return
        }

        if (confirm('{{ trans('global.areYouSure') }}')) {
            $.ajax({
              headers: {'x-csrf-token': _token},
              method: 'POST',
              url: "{{ route('admin.users.massUpdate') }}",
              data: { 
                  ids: ids, 
                  _method: 'PATCH',
                  status_id: $('#status_id').val()
              }
            })
              .done(function () { location.reload() })
        }  
    }
    
    function resetPassword()
    {
        var ids = $('#users-datatable').DataTable().rows({ selected: true }).ids().toArray()
        if (ids.length === 0) {
            alert('{{ trans('global.datatables.zero_selected') }}')
            return
        }

        if (confirm('{{ trans('global.areYouSure') }}')) {
            $.ajax({
              headers: {'x-csrf-token': _token},
              method: 'POST',
              url: "{{ route('admin.users.massUpdate') }}",
              data: { 
                  ids: ids, 
                  _method: 'PATCH',
                  password: $('#password').val()
              }
            })
              .done(function () { location.reload() })
        }  
    }
    
    function destroyUser(id)
    {
        if (confirm('{{ trans('global.areYouSure') }}')) {
            $.ajax({
                headers: {'x-csrf-token': _token},
                method: 'POST',
                url: 'users/'+id,
                data: { _method: 'DELETE' }})
                .done(function () { 
                     $("#"+id).closest('tr').remove();
                })
        }
    }
    
    function  massDestroyUser()
    {
        var ids = $('#users-datatable').DataTable().rows({ selected: true }).ids().toArray()
        if (ids.length === 0) {
          alert('{{ trans('global.datatables.zero_selected') }}')
          return
        }

        if (confirm('{{ trans('global.areYouSure') }}')) {
          $.ajax({
            headers: {'x-csrf-token': _token},
            method: 'POST',
            url: '{{ route('admin.users.massDestroy') }}',
            data: { ids, _method: 'DELETE' }})
            .done(function () { location.reload() })
        }
        
    }   
    
$(document).ready( function () {
    $('#login_password_show').on('change', function(){
        $('#password').attr('type',$('#checkbox').prop('checked')==true?"text":"password"); 
    });


    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    var table = $('#users-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin/users/list') }}",
        columns: [
                 { data: 'check'},
                 { data: 'username' },
                 { data: 'firstname' },
                 { data: 'lastname' },
                 { data: 'email' },
                 { data: 'email_verified_at' },
                 { data: 'status' },
                 { data: 'action' }
                ],
        buttons: dtButtons
    });
    //align header/body
    $(".dataTables_scrollHeadInner").css({"width":"100%"});
    $(".table ").css({"width":"100%"});
 });
</script>

<!--
<script>
$(function () {
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.users.massDestroy') }}",
    className: 'btn-danger',

    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

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
  let resetPasswordButtonTrans = '{{ trans('global.datatables.resetPassword') }}'
  
  let resetPasswordButton = {
    text: resetPasswordButtonTrans,
    url: "{{ route('admin.users.massUpdate') }}",
    className: 'btn-danger',

    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { 
              ids: ids, 
              _method: 'PATCH',
              password: $('#password').val()
          }
        })
          .done(function () { location.reload() })
      }
    }
  }     
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('user_delete')
  dtButtons.push(deleteButton)
@endcan
@can('user_delete')
  dtButtons.push(resetPasswordButton)
@endcan

   

  $('#users').DataTable({ 
      buttons: dtButtons,  
  })
  
    
})

</script>-->
@endsection