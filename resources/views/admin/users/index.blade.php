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
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('global.user.fields.username') }}
                        </th>
                        <th>
                            {{ trans('global.user.fields.firstname') }}
                        </th>
                        <th>
                            {{ trans('global.user.fields.lastname') }}
                        </th>
                        <th>
                            {{ trans('global.user.fields.email') }}
                        </th>
                        <th>
                            {{ trans('global.user.fields.email_verified_at') }}
                        </th>
                        <th>
                            {{ trans('global.organization.title') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                        <tr data-entry-id="{{ $user->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $user->username ?? '' }}
                            </td>
                            <td>
                                {{ $user->firstname ?? '' }}
                            </td>
                            <td>
                                {{ $user->lastname ?? '' }}
                            </td>
                            <td>
                                {{ $user->email ?? '' }}
                            </td>
                            <td>
                                {{ $user->email_verified_at ?? '' }}
                            </td>
                            <td>
                                @foreach($user->organizations as $key => $org)
                                <span class="badge badge-info">{{ $org->title }} @ {{ $org->roles->first()->title }} 
                                    
                                    @can('user_expel')
                                        <form action="/admin/users/{{ $user->id }}/organization/{{ $org->id }}/expel" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="x">
                                        </form>
                                    @endcan
                                    
                                </span>
                                @endforeach
                                @can('user_enrol')
                                    <form action="/admin/users/{{ $user->id }}/organization/enrol" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <label for="organizations">{{ trans('global.organization.title') }}*
                                            <span class="btn btn-info btn-xs select-all">Select all</span>
                                            <span class="btn btn-info btn-xs deselect-all">Deselect all</span>
                                        </label>
                                        <select name="organizations[]" id="organizations" class="form-control select2" multiple="multiple">
                                            @foreach($organizations as $organization)
                                                <option value="{{ $organization->id }}">
                                                    {{ $organization->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input id="role_id" name="role_id" type="integer" value="2">
                                        <input type="submit" class="btn btn-xs btn-info" value="+">
                                    </form>
                                @endcan
                            </td>
                            <td>
                                @can('user_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.users.show', $user->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('user_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', $user->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('user_delete')
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row ">
    <div class="col-sm-12">
        <div class="card">  
            <div class="card-header">
                <ul class="nav nav-pills">
                    <!--@can('user_resetPassword')-->
                    <!--@endcan-->
                        <li id="nav_tab_password" class="nav-item">
                            <a href="#tab_password" class="nav-link" data-toggle="tab">Passwort</a>
                        </li>

                    <!--@canany(['user_enroleToGroup', 'user_expelFromGroup'])-->
                    <!--@endcanany-->
                        <li id="nav_tab_group" class="nav-item">
                            <a href="#tab_group" class="nav-link" data-toggle="tab">Lerngruppe</a>
                        </li>

                    <!--@can('user_updateRole')-->
                    <!--@endcan-->
                        <li id="nav_tab_institution" class="nav-item">
                            <a href="#tab_institution" class="nav-link" data-toggle="tab">Institution / Rolle</a>
                        </li>

                    <!--@can('user_userListComplete')-->
                    <!--@endcan-->
                        <li id="nav_tab_register" class="nav-item">
                            <a href="#tab_register" class="nav-link" data-toggle="tab">Registerung bestätigen</a>
                        </li>

                    <!--@can('user_delete')-->
                    <!--@endcan-->
                        <li id="nav_tab_delete" class="nav-item">
                            <a href="#tab_delete" class="nav-link" data-toggle="tab"><span class="text-danger">löschen</span></a>
                        </li>
                </ul>
            </div>
            <div class="card-body">
              <div class="tab-content">
                    <!--@can('user_resetPassword')-->
                    <!--@endcan-->
                        <div id="tab_password" class="tab-pane active row " >
                            <form id='userlist_pw'   method='post' action="index.php?action=user">
                                <div class="form-horizontal col-xs-12">
                                @include ('forms.input.text', ["model" => "user", "field" => "pw_info", "placeholder" => "New Password",  "value" => ""])
                                @include ('forms.input.info', ["value" => "Neues Passwort für markierte Benutzer festlegen. Passwort muss mind. 6 Zeichen lang sein."])
                                @include ('forms.input.checkbox', ["model" => "user", "field" => "showpassword", "value" => ""])
                                @include ('forms.input.checkbox', ["model" => "user", "field" => "confirmed", "value" => ""])
                                @include ('forms.input.button', ["field" => "confirmed", "type" => "submit", "class" => "btn btn-default pull-right", "icon" => "fa fa-lock", "label" => "Passwort zurücksetzen"])
                                </div>
                            </form>
                        </div>
                    

                    <!--@canany(['user_enroleToGroup', 'user_expelFromGroup'])-->
                        <div id="tab_group" class="tab-pane row " >
                            <form id='userlist_groups'   method='post' action="index.php?action=user">
                                <div class="form-horizontal col-xs-12">
                                    {Form::info(['id' => 'group_info', 'content' => 'Markierte Benutzer in Lerngruppe ein bzw. ausschreiben.<br> <strong>Benutzer muss an der entsprechenden Institution eingeschrieben sein, damit  die Lerngruppe angezeigt wird.</strong>'])}
                                    {if isset($myInstitutions)}
                                        {Form::input_select('institution_group', 'Institution', $myInstitutions, 'institution', 'id', $my_institution_id, null, "getValues('group', this.value, 'groups');")}
                                    {/if} 
                                {if isset($groups_array)}
                                    {Form::input_select_multiple(['id' => 'groups', 'label' => 'Lerngruppe', 'select_data' => $groups_array, 'select_label' => 'group, semester', 'select_value' => 'id', 'input' => null, 'error' => null, 'limiter' => ', ' ])}
                                    <div class="btn-group pull-right" role="group" aria-label="...">
                                    @can('user_enroleToGroup')
                                        {Form::input_button(['id' => 'enroleGroups', 'label' => 'einschreiben', 'icon' => 'fa fa-plus-circle', 'class' => 'btn btn-default pull-left'])}
                                    @endcan
                                    @can('user_expelFromGroup')
                                        {Form::input_button(['id' => 'expelGroups', 'label' => 'ausschreiben', 'icon' => 'fa fa-minus-circle', 'class' => 'btn btn-default pull-left'])}
                                    @endcan
                                    </div>
                                {/if}
                                </div>
                            </form>
                        </div>
                    <!--@endcanany-->

                    <!--@can('user_updateRole')-->
                        <div id="tab_institution" class="tab-pane row " >
                            <form id='userlist_institutions'   method='post' action="index.php?action=user">
                                <div class="form-horizontal col-xs-12">
                                        {Form::info(['id' => 'role_info', 'content' => 'Beim Zuweisen einer Rolle werden die markierten Nutzer automatisch in die aktuelle/ausgewählte Institution eingeschrieben bzw. die Daten aktualisiert.'])}
                                    {if isset($myInstitutions)}
                                        {Form::input_select('institution', 'Institution', $myInstitutions, 'institution', 'id', $my_institution_id, null)}
                                    {/if}    
                                    {Form::input_select('roles', 'Benutzer-Rolle', $roles, 'role', 'id', $institution_std_role, null)}

                                    <div class="btn-group pull-right" role="group" aria-label="...">
                                    {if checkCapabilities('user:enroleToInstitution', $my_role_id, false)}
                                        {Form::input_button(['id' => 'enroleInstitution', 'label' => 'Rolle zuweisen / einschreiben', 'icon' => 'fa fa-plus-circle', 'class' => 'btn btn-default'])}
                                    {/if}
                                    {if checkCapabilities('user:expelFromInstitution', $my_role_id, false)}
                                        {Form::input_button(['id' => 'expelInstitution', 'label' => 'ausschreiben', 'icon' => 'fa fa-minus-circle', 'class' => 'btn btn-default'])}
                                    {/if} 
                                    </div>
                                </div>
                            </form>
                        </div>
                    <!--@endcan-->

                    <!--@can('user_userListComplete')-->
                        <div id="tab_register" class="tab-pane row " >
                            <form id='userlist_register'   method='post' action="index.php?action=user">
                                <div class="form-horizontal col-xs-12">
                                    {Form::info(['id' => 'user_info', 'content' => 'Registerung für die markierten Benutzer bestätigen.'])}
                                    {Form::input_button(['id' => 'acceptUser', 'label' => 'Benutzer bestätigen', 'icon' => 'fa fa-user-plus', 'class' => 'btn btn-default'])}
                                </div>
                            </form>
                        </div>
                    <!--@endcan-->

                    <!--@can('user_delete')-->
                        <div id="tab_delete" class="tab-pane row" >
                            <form id='userlist_delete'   method='post' action="index.php?action=user">
                                <div class="form-horizontal col-xs-12">
                                    {Form::info(['id' => 'user_info', 'content' => 'Markierte Benutzer löschen.'])}
                                    {Form::input_button(['id' => 'submitdeleteUser', 'label' => 'löschen', 'icon' => 'fa fa-minus-circle', 'type' => 'button', 'onclick' => 'userdelete()', 'class' => 'btn btn-default pull-right'])}
                                    {Form::input_button(['id' => 'deleteUser', 'label' => 'löschen', 'icon' => 'fa fa-minus-circle', 'class' => 'hidden'])}
                                </div>
                            </form>
                        </div>
                    <!--@endcan-->

                </div><!-- ./tab-content -->
             </div>
        </div>
    </div><!-- ./col-xs-12 -->  

</div>

@endsection
@section('scripts')
@parent
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
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('user_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection