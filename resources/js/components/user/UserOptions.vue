<template>
    <div class="px-3">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills">
                    <li
                        v-permission="'user_reset_password'"
                        id="nav_tab_password"
                        class="nav-item"
                    >
                        <a
                            href="#tab_password"
                            class="nav-link"
                            data-bs-toggle="tab"
                        >
                            {{ trans('global.login_password') }}
                        </a>
                    </li>
                    <li
                        v-permission="'group_enrolment'"
                        id="nav_tab_group"
                        class="nav-item"
                    >
                        <a
                            href="#tab_group"
                            class="nav-link active"
                            data-bs-toggle="tab"
                        >
                            {{ trans('global.group.title') }}
                        </a>
                    </li>
                    <li
                        v-permission="'organization_enrolment'"
                        id="nav_tab_organization"
                        class="nav-item"
                    >
                        <a
                            href="#tab_organization"
                            class="nav-link"
                            data-bs-toggle="tab"
                        >
                            {{ trans('global.organization.title') }} / {{ trans('global.role.title') }}
                        </a>
                    </li>
                    <li
                        v-permission="'is_admin'"
                        id="nav_tab_register"
                        class="nav-item"
                    >
                        <a
                            href="#tab_register"
                            class="nav-link"
                            data-bs-toggle="tab"
                        >
                            {{ trans('global.registration_confirm') }}
                        </a>
                    </li>
                    <li
                        v-permission="'user_delete'"
                        id="nav_tab_delete"
                        class="nav-item"
                    >
                        <a
                            href="#tab_delete"
                            class="nav-link"
                            data-bs-toggle="tab"
                        >
                            {{ trans('global.user.delete') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div
                        v-permission="'user_reset_password'"
                        id="tab_password"
                        class="tab-pane row"
                    >
                        <div class="form-horizontal col-xs-12 px-4">
                            <div class="form-group">
                                <label>
                                    Neues Passwort für markierte Benutzer festlegen. Passwort muss mind. 6 Zeichen lang sein.
                                </label>
                            </div>
                            <div class="form-group">
                                <input
                                    :type="form.checked ? 'text' : 'password'"
                                    id="password"
                                    class="form-control"
                                    v-model="form.password"
                                    placeholder="Passwort eingeben"
                                />
                            </div>
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    id="password_show"
                                    class="form-check-label"
                                    v-model="form.checked"
                                />
                                <label
                                    for="input-password_show"
                                    class="form-check-label ps-2"
                                >
                                    Passwort anzeigen
                                </label>
                            </div>
                            <button
                                id="confirmed"
                                type="button"
                                class="btn btn-default float-end mt-3"
                                @click="resetPassword()"
                            >
                                <i class="fa fa-lock me-2"></i>
                                {{ trans('global.reset_password') }}
                            </button>
                        </div>
                    </div>

                    <div
                        v-permission="'group_enrolment'"
                        id="tab_group"
                        class="tab-pane active row"
                    >
                        <div class="form-horizontal col-xs-12 px-4">
                            <div class="form-group">
                                <label>
                                    Markierte Benutzer in Lerngruppe ein bzw. ausschreiben.<br/>
                                    Benutzer muss an der entsprechenden Institution eingeschrieben sein, damit  die Lerngruppe angezeigt wird.
                                </label>
                            </div>
                            <div class="form-group pt-2">
                                <Select2
                                    id="group_subscriptions"
                                    url="/groups"
                                    model="group"
                                    :multiple="true"
                                    :selected="form.group_ids"
                                    @selectedValue="id => form.group_ids = id"
                                />
                            </div>
                            <button
                                id="enroleToGroup"
                                type="button"
                                class="btn btn-default float-end mt-3"
                                @click="enroleToGroup()"
                            >
                                <i class="fa fa-plus me-2"></i>
                                {{ trans('global.user.enrol') }}
                            </button>
                            <button
                                id="expelFromGroup"
                                type="button"
                                class="btn btn-default float-end mt-3"
                                @click="expelFromGroup()"
                            >
                                <i class="fa fa-minus me-2"></i>
                                {{ trans('global.user.expel') }}
                            </button>
                        </div>
                    </div>

                    <div v-if="checkPermission('organization_enrolment')"
                        id="tab_organization"
                        class="tab-pane row"
                    >
                        <div class="form-horizontal col-xs-12 px-4">
                            <div class="form-group">
                                <label>
                                    Beim Zuweisen einer Rolle werden die markierten Nutzer automatisch in die aktuelle/ausgewählte Institution eingeschrieben bzw. die Daten aktualisiert.
                                </label>
                            </div>
                            <div class="form-group pt-2">
                                <Select2
                                    id="role_organization_id"
                                    url="/organizations"
                                    model="organization"
                                    :selected="form.role_organization_id"
                                    @selectedValue="id => form.role_organization_id = id"
                                />
                            </div>
                            <div class="form-group pt-2">
                                <Select2
                                    id="role_id"
                                    url="/roles"
                                    model="role"
                                    :selected="form.role_id"
                                    @selectedValue="id => form.role_id = id"
                                />
                            </div>
                            <button
                                id="enroleToOrganization"
                                type="button"
                                class="btn btn-default float-end mt-3"
                                @click="enroleToOrganization()"
                            >
                                <i class="fa fa-plus me-2"></i>
                                {{ trans('global.role.enrol') }}
                            </button>
                            <button
                                id="expelFromOrganization"
                                type="button"
                                class="btn btn-default float-end mt-3"
                                @click="expelFromOrganization()"
                            >
                                <i class="fa fa-minus me-2"></i>
                                {{ trans('global.role.expel') }}
                            </button>
                        </div>
                    </div>

                    <div
                        id="tab_register"
                        class="tab-pane row"
                    >
                        <div class="form-horizontal col-xs-12 px-4">
                            <div class="form-group">
                                <label>
                                    Markierte Benutzer in Lerngruppe ein bzw. ausschreiben.<br/>
                                    Benutzer muss an der entsprechenden Institution eingeschrieben sein, damit  die Lerngruppe angezeigt wird.
                                </label>
                            </div>
                            <div class="form-group pt-2">
                                <Select2
                                    id="status_definition_id"
                                    url="/statusdefinitions"
                                    model="statusdefinition"
                                    :selected="form.status_definition_id"
                                    @selectedValue="id => form.status_definition_id = id"
                                />
                            </div>
                            <button
                                id="setUserStatus"
                                type="button"
                                class="btn btn-default float-end mt-3"
                                @click="setUserStatus()"
                            >
                                <i class="fa fa-lock me-2"></i>
                                {{ trans('global.user.set_status') }}
                            </button>
                        </div>
                    </div>

                    <div
                        v-permission="'user_delete'"
                        id="tab_delete"
                        class="tab-pane row"
                    >
                        <div class="form-horizontal col-xs-12 px-4">
                            {{ trans('global.forceDelete') }}
                            <button
                                id="deleteUser"
                                type="button"
                                class="btn btn-danger float-end mt-3"
                                @click="deleteUser()"
                            >
                                <i class="fa fa-trash me-2"></i>
                                {{ trans('global.user.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { useDatatableStore } from "../../store/datatables";
import Form from "form-backend-validation";
import Select2 from "../forms/Select2.vue";

export default {
    components: { Select2 },
    setup() {
        return { store: useDatatableStore() }
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                username: '',
                firstname: '',
                lastname: '',
                email: '',
                password: '',
                checked: false,
                group_ids: null,
                role_organization_id: null,
                role_id: null,
                status_definition_id: null
            }),
        }
    },
    methods: {
        resetPassword() {
            axios.patch('/users/massUpdate', {
                ids: this.store.getDatatable('users')?.selectedItems.map(x => x.id),
                password: this.form.password,
            })
            .then(r => {
                this.successNotification(window.trans.global.reset_password_success);
            })
            .catch(e => {
                this.errorNotification(window.trans.global.reset_password_error);
                console.log(e.response);
            });
        },
        feedbackSuccess(r, message) {
            if (r.data !== '') {
                this.successNotification(message);
            }
        },
        feedbackError(e) {
            this.errorNotification(window.trans.global.user.enrol_error);
            console.log(e.response);
        },
        enroleToGroup() {
            let enrollment_list = this.generateGroupProcessList();

            axios.post('/groups/enrol', { enrollment_list: enrollment_list })
                .then(r => { this.feedbackSuccess(r, window.trans.global.user.enrol_success); })
                .catch(e => { this.feedbackError(e); });
        },
        expelFromGroup() {
            let expel_list = this.generateGroupProcessList();

            axios.delete('/groups/expel', {
                    data: {
                        expel_list: expel_list,
                    }
                })
                .then(r => { this.feedbackSuccess(r, window.trans.global.user.expel_success); })
                .catch(e => { this.feedbackError(e); });
        },
        enroleToOrganization() {
            let enrollment_list = this.generateOrganizationProcessList();

            axios.post('/organizations/enrol', { enrollment_list: enrollment_list })
                .then(r => { this.feedbackSuccess(r, window.trans.global.user.enrol_success); })
                .catch(e => { this.feedbackError(e); });
        },
        expelFromOrganization() {
            let expel_list = this.generateOrganizationProcessList();

            axios.delete('/organizations/expel', {
                    data: {
                        expel_list: expel_list,
                    }
                })
                .then(r => { this.feedbackSuccess(r, window.trans.global.user.expel_success); })
                .catch(e => { this.feedbackError(e); });
        },
        generateGroupProcessList() {
            let ids = this.store.getDatatable('users')?.selectedItems.map(x => x.id);
            let processList = [];
            if (typeof (ids) != 'undefined'){
                for (let i = 0; i < ids.length; i++) {
                    processList.push({
                        user_id: ids[i],
                        group_id: this.form.group_ids
                    });
                }
            } else {
                this.errorNotification(window.trans.global.user.no_selection);
            }
            return processList;
        },
        generateOrganizationProcessList() {
            let ids = this.store.getDatatable('users')?.selectedItems.map(x => x.id);
            var processList = [];
            if (this.form.role_organization_id[0] && this.form.role_id[0] && (typeof (ids) != 'undefined')){
                for (let i = 0; i < ids.length; i++) {
                    processList.push({
                        user_id: ids[i],
                        organization_id: this.form.role_organization_id[0],
                        role_id: this.form.role_id[0]
                    });
                }
            } else {
                this.errorNotification(trans('global.user.enrol_error'));
            }
            return processList;
        },
        setUserStatus() {
            if (this.form.status_definition_id !== null) {
                axios.patch('/users/massUpdate', {
                    ids: this.store.getDatatable('users')?.selectedItems.map(x => x.id),
                    status_id: this.form.status_definition_id[0],
                })
                    .then(r => {
                        this.successNotification(window.trans.global.user.set_status_success);
                    })
                    .catch(e => {
                        this.errorNotification(window.trans.global.user.set_status_error);
                        console.log(e.response);
                    });
            } else {
                this.errorNotification(window.trans.global.user.set_status_error);
            }
        },
        deleteUser() {
            axios.delete('/users/massDestroy',
                {
                    data: {
                        ids: this.store.getDatatable('users')?.selectedItems.map(x => x.id),
                    }
                })
                .then(r => {
                    this.successNotification(window.trans.global.user.delete_success);
                    window.location.reload()
                })
                .catch(e => {
                    this.errorNotification(window.trans.global.user.delete_error);
                    console.log(e.response);
                });
        },
        successNotification(message) {
            this.toast.success(message, { timeout: 3000 });
        },
        errorNotification(message) {
            this.toast.error(message, { timeout: 3000 });
        },
    },
}
</script>