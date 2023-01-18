@extends('layouts.master')
@section('title')
    {{ trans('global.user.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.user.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-user"
               class="btn btn-success" href="{{ route("users.create") }}">
               {{ trans('global.user.create') }}
            </a>
            <a class="btn btn-success" href="{{ route("users.createImport") }}" >
                {{ trans('global.user.import') }}
            </a>
        </div>
    </div>
@endcan

<table id="users-datatable"
       class="table table-hover datatable">
    <thead>
        <tr>
            <th width="10"></th>
            <th>{{ trans('global.user.fields.username') }}</th>
            <th>{{ trans('global.user.fields.firstname') }}</th>
            <th>{{ trans('global.user.fields.lastname') }}</th>
            <th>{{ trans('global.user.fields.email') }}</th>
            <th>{{ trans('global.user.fields.deleted_at') }}</th>
            <th></th>
        </tr>
    </thead>
</table>

<div class="row ">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills">
                    @can('user_reset_password')
                        <li id="nav_tab_password" class="nav-item">
                            <a href="#tab_password" class="nav-link active" data-toggle="tab">Passwort</a>
                        </li>
                    @endcan
                    @can('group_enrolment')
                        <li id="nav_tab_group" class="nav-item">
                            <a href="#tab_group" class="nav-link" data-toggle="tab">Lerngruppe</a>
                        </li>
                    @endcan
                    @can('organization_enrolment')
                        <li id="nav_tab_organization" class="nav-item">
                            <a href="#tab_organization" class="nav-link" data-toggle="tab">Institution / Rolle</a>
                        </li>
                        <li id="nav_tab_register" class="nav-item">
                            <a href="#tab_register" class="nav-link" data-toggle="tab">Registerung bestätigen</a>
                        </li>
                    @endcan
                    @can('user_delete')
                        <li id="nav_tab_delete" class="nav-item">
                            <a href="#tab_delete" class="nav-link" data-toggle="tab"><span class="text">löschen</span></a>
                        </li>
                    @endcan
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    @can('user_reset_password')
                        <div id="tab_password" class="tab-pane active row " >
                            <div class="form-horizontal col-xs-12 px-4">
                            @include ('forms.input.info', ["value" => "Neues Passwort für markierte Benutzer festlegen. Passwort muss mind. 6 Zeichen lang sein."])
                            @include ('forms.input.password', ["model" => "user", "field" => "password", "placeholder" => "New Password", "type" => "password", "value" => ""])
<!--                            @include ('forms.input.checkbox', ["field" => "login_password_confirmation", "value" => ""])-->
                            @include ('forms.input.button', ["onclick" => "resetPassword()", "field" => "confirmed", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-lock", "label" => "Passwort zurücksetzen"])
                            </div>
                        </div>
                    @endcan

                    @can('group_enrolment')
                        <div id="tab_group" class="tab-pane row " >
                            <div class="form-horizontal col-xs-12 px-4">
                                @include ('forms.input.info', ["value" => "Markierte Benutzer in Lerngruppe ein bzw. ausschreiben.\nBenutzer muss an der entsprechenden Institution eingeschrieben sein, damit  die Lerngruppe angezeigt wird."])

                                @include ('forms.input.select',
                                    ["model" => "group",
                                    "url" => "groups",
                                    "placeholder" => trans('global.pleaseSelect'),
                                    "show_label" => true,
                                    "multiple" => true,
                                    "field" => "user_organization_group_id",
                                  /*  "options"=> $groups,*/
                                    "option_label" => "title",
                                   /* "optgroup" => $organizations,*/
                                    "optgroup_reference_field" => "organization_id",
                                    "value" =>  old('group_id', isset($user->current_group_id) ?? '')])

                                <div class="btn-group pull-right" role="group" aria-label="...">
                                    @include ('forms.input.button', ["onclick" => "enroleToGroup()", "field" => "enroleToGroup", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-plus", "label" => "In Gruppe einschreiben"])
                                    @include ('forms.input.button', ["onclick" => "expelFromGroup()", "field" => "expelFromGroup", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-minus", "label" => "Aus Gruppe ausschreiben"])
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('organization_enrolment')
                        <div id="tab_organization" class="tab-pane row " >
                            <div class="form-horizontal col-xs-12 px-4">
                                @include ('forms.input.info', ["value" => "Beim Zuweisen einer Rolle werden die markierten Nutzer automatisch in die aktuelle/ausgewählte Institution eingeschrieben bzw. die Daten aktualisiert."])
                                @include ('forms.input.select',
                                    ["model" => "organization",
                                    "url" => "organizations",
                                    "show_label" => true,
                                    "placeholder" => trans('global.pleaseSelect'),
                                    "field" => "role_organization_id",
                                    "option_label" => "title",
                                    "value" =>  old('organization_id', isset($user->current_organization_id) ? $user->current_organization_id : '')])
                                 @include ('forms.input.select',
                                    ["model" => "role",
                                    "url" => "roles",
                                    "placeholder" => trans('global.pleaseSelect'),
                                    "show_label" => true,
                                    "field" => "role_id",
                                    "option_label" => "title",
                                    "value" =>  old('role_id', isset($user->current_role_id) ? $user->current_role_id : '')])
                                <div class="btn-group pull-right" role="group" aria-label="...">
                                    @include ('forms.input.button', ["onclick" => "enroleToOrganization()", "field" => "enroleToOrganization", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-plus", "label" => "Rolle zuweisen / einschreiben"])
                                    @include ('forms.input.button', ["onclick" => "expelFromOrganization()", "field" => "expelFromOrganization", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-minus", "label" => "Rolle entziehen /ausschreiben"])
                                </div>
                            </div>
                        </div>

                        <div id="tab_register" class="tab-pane row " >
                            <div class="form-horizontal col-xs-12  px-4">
                                 @include ('forms.input.select',
                                ["model" => "statusdefinition",
                                "url" => "statusdefinitions",
                                "placeholder" => trans('global.pleaseSelect'),
                                "show_label" => true,
                                "field" => "status_definition_id",
                                "option_id" => "status_definition_id",
                                "option_label" => "lang_de",
                                "value" => old('status_definition_id', isset($user->status_definition_id) ? $user->status_definition_id : '') ])
                                @include ('forms.input.button', ["onclick" => "setStatus()", "field" => "acceptUser", "type" => "submit", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-lock", "label" => "Benutzerstatus ändern"])
                            </div>
                        </div>
                    @endcan
                    @can('user_delete')
                        <div id="tab_delete" class="tab-pane row" >
                            <div class="form-horizontal col-xs-12 px-4">

                                {{ trans('global.forceDelete') }}
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
function getDatatablesIds(selector){
    return $(selector).DataTable().rows({ selected: true }).ids().toArray();
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

function generateGroupProcessList(ids){
    var processList = [];
    for (i = 0; i < ids.length; i++) {
        processList.push({
            user_id: ids[i],
            group_id: $('#user_organization_group_id').val(),
        });
    }
    return processList;
}

function enroleToGroup() {
    var ids = getDatatablesIds('#users-datatable');
    sendRequest('POST', '/groups/enrol', ids, { enrollment_list: generateGroupProcessList(ids), _method: 'POST'});
}

function expelFromGroup() {
    var ids = getDatatablesIds('#users-datatable');
    sendRequest('POST', '/groups/expel', ids, { expel_list: generateGroupProcessList(ids), _method: 'DELETE'});
}

function generateOrganizationProcessList(ids){
    var processList = [];
    for (i = 0; i < ids.length; i++) {
        processList.push({
            user_id: ids[i],
            organization_id: $('#role_organization_id').val(),
            role_id: $('#role_id').val()
        });
    }
    return processList;
}

function enroleToOrganization() {
    var ids = getDatatablesIds('#users-datatable');
    sendRequest('POST', '/organizations/enrol', ids, { enrollment_list: generateOrganizationProcessList(ids), _method: 'POST'});
}

function expelFromOrganization() {
    var ids = getDatatablesIds('#users-datatable');
    sendRequest('POST', '/organizations/expel', ids, { expel_list: generateOrganizationProcessList(ids), _method: 'DELETE'});
}

function setStatus() {
    var ids = getDatatablesIds('#users-datatable');
    sendRequest('POST', "{{ route('users.massUpdate') }}", ids, { ids: ids, _method: 'PATCH', status_definition_id: $('#status_definition_id').val() });
}

function resetPassword() {
    var ids = getDatatablesIds('#users-datatable');
    sendRequest('POST', "{{ route('users.massUpdate') }}", ids, { ids: ids, _method: 'PATCH', password: $('#password').val() });
}

function massDestroyUser() {
    var ids = getDatatablesIds('#users-datatable');
    sendRequest('POST', "{{ route('users.massDestroy') }}", ids, { ids: ids, _method: 'DELETE' });
}

$( function () {
    $('#login_password_show').on('change', function(){
        $('#password').attr('type',$('#checkbox').prop('checked')==true?"text":"password");
    });

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)


    var table = $('#users-datatable').DataTable({
        ajax: "{{ url('users/list') }}",
        columns: [
            {data: 'check'},
            {data: 'username'},
            {data: 'firstname'},
            {data: 'lastname'},
            {data: 'email'},
            {data: 'deleted_at', "defaultContent": null},
            {data: 'action'}
        ],
        bStateSave: true,
        fnStateSave: function (oSettings, oData) {
            localStorage.setItem('DataTables', JSON.stringify(oData));
        },
        fnStateLoad: function (oSettings) {
            return JSON.parse(localStorage.getItem('DataTables'));
        },
        buttons: dtButtons
    });

});
</script>

@endsection
