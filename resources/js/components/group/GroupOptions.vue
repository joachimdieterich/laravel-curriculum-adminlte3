<template >
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills">
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
                            {{ trans('global.curriculum.title') }}
                        </a>
                    </li>
                    <li
                        v-permission="'group_delete'"
                        id="nav_tab_delete"
                        class="nav-item"
                    >
                        <a
                            href="#tab_delete"
                            class="nav-link"
                            data-bs-toggle="tab"
                        >
                            <span class="text">{{ trans('global.delete') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div
                        v-permission="'group_enrolment'"
                        id="tab_group"
                        class="tab-pane row fade active show"
                    >
                        <div class="form-horizontal col-xs-12 px-4">
                            <div class="form-group">
                                <label>Markierte Gruppen in Lehrplan ein- bzw. ausschreiben.</label>
                            </div>
                            <div class="form-group pt-2">
                                <Select2
                                    id="group-curricula"
                                    url="/curricula"
                                    model="curriculum"
                                    :multiple="true"
                                    :selected="form.group_curricula_ids"
                                    @selectedValue="id => form.group_curricula_ids = id"
                                />
                            </div>
                            <button
                                id="group-enrol-to-curricula"
                                type="button"
                                class="btn btn-default float-end mt-3"
                                @click="enrolToCurricula()"
                            >
                                <i class="fa fa-plus me-2"></i>
                                {{ trans('global.group.enrol') }}
                            </button>
                            <button
                                id="group-expel-from-curricula"
                                type="button"
                                class="btn btn-default float-end mt-3"
                                @click="expelFromCurricula()"
                            >
                                <i class="fa fa-minus me-2"></i>
                                {{ trans('global.group.expel') }}
                            </button>
                        </div>
                    </div>
                    <div
                        v-permission="'user_delete'"
                        id="tab_delete"
                        class="tab-pane row fade"
                    >
                        <div class="form-horizontal col-xs-12 px-4">
                            {{ trans('global.forceDelete') }}
                            <button
                                id="deleteUser"
                                type="button"
                                name="deleteUser"
                                class="btn btn-danger float-end mt-3"
                                @click="deleteUser()"
                            >
                                <i class="fa fa-trash me-2"></i>
                                {{ trans('global.group.delete') }}
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
                group_curricula_ids: [],
            }),
        }
    },
    methods: {
        enrolToCurricula() {
            axios.post('/curricula/enrol', { enrollment_list: this.generateGroupProcessList() })
                .then(r => { this.feedbackSuccess(r); })
                .catch(e => { this.feedbackError(e); });
        },
        expelFromCurricula() {
            axios.delete('/curricula/expel', {
                    data: {
                        expel_list: this.generateGroupProcessList(),
                    }
                })
                .then(r => { this.feedbackSuccess(r); })
                .catch(e => { this.feedbackError(e); });
        },
        generateGroupProcessList() {
            let ids = this.store.getDatatable('groups')?.selectedItems.map(x => x.id);
            let processList = [];

            if (typeof (ids) != 'undefined') {
                for (let i = 0; i < ids.length; i++) {
                    processList.push({
                        group_id: ids[i],
                        curriculum_id: this.form.group_curricula_ids
                    });
                }
            } else {
                this.errorNotification(window.trans.global.group.enrol_error);
            }
            return processList;
        },
        feedbackSuccess(r) {
            if (r.data !== '') {
                this.successNotification(window.trans.global.group.enrol_success);
            }
        },
        feedbackError(e) {
            this.errorNotification(window.trans.global.group.enrol_error);
            console.log(e.response);
        },
        deleteUser() {
            axios.delete('/groups/massDestroy',
                {
                    data: {
                        ids: this.store.getDatatable('groups')?.selectedItems.map(x => x.id),
                    }
                })
                .then(r => {
                    this.successNotification(window.trans.global.group.delete_success);
                    window.location.reload()
                })
                .catch(e => {
                    this.errorNotification(window.trans.global.group.delete_error);
                    console.log(e.response);
                });
        },
        successNotification(message) {
            this.toast.success(message);
        },
        errorNotification(message) {
            this.toast.error(message);
        },
    },
}
</script>