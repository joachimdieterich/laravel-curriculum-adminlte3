<template >
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills">
                    <li v-permission="'group_enrolment'"
                        id="nav_tab_group" class="nav-item">
                        <a href="#tab_group" class="nav-link active" data-toggle="tab">
                            {{ trans('global.curriculum.title') }}
                        </a>
                    </li>
                    <li v-permission="'group_delete'"
                        id="nav_tab_delete" class="nav-item">
                        <a href="#tab_delete" class="nav-link" data-toggle="tab"><span class="text">löschen</span></a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div v-permission="'group_enrolment'"
                         id="tab_group" class="tab-pane active row" >
                        <div class="form-horizontal col-xs-12 px-4">
                            <div id="form_group"
                                 class="form-group">
                                <label>
                                    Markierte Benutzer in Lerngruppe ein bzw. ausschreiben.
                                    Benutzer muss an der entsprechenden Institution eingeschrieben sein, damit  die Lerngruppe angezeigt wird.
                                </label>
                            </div>
                            <div class="form-group pt-2 ">
                                <Select2
                                    id="group_curricula"
                                    name="group_curricula"
                                    url="/curricula"
                                    model="curriculum"
                                    :multiple="true"
                                    :selected="this.form.group_curricula_ids"
                                    @selectedValue="(id) => {
                                        this.form.group_curricula_ids = id;
                                    }"
                                >
                                </Select2>
                            </div>
                            <button
                                id="enroleToCurricula"
                                type="button"
                                name="enroleToCurricula"
                                class="btn btn-default pull-right mt-3"
                                @click="enroleToCurricula()"
                            >
                                <i class="fa fa-plus mr-2"></i>
                                {{ trans('global.group.enrol') }}
                            </button>
                            <button
                                id="expelFromCurricula"
                                type="button"
                                name="expelFromCurricula"
                                class="btn btn-default pull-right mt-3"
                                @click="expelFromCurricula()"
                            >
                                <i class="fa fa-minus mr-2"></i>
                                {{ trans('global.group.expel') }}
                            </button>
                        </div>
                    </div>
                    <div v-permission="'user_delete'"
                         id="tab_delete" class="tab-pane row" >
                        <div class="form-horizontal col-xs-12 px-4">
                            {{ trans('global.forceDelete') }}
                            <button
                                id="deleteUser"
                                type="button"
                                name="deleteUser"
                                class="btn btn-danger pull-right mt-3"
                                @click="deleteUser()"
                            >
                                <i class="fa fa-trash mr-2"></i>
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
import { useToast } from "vue-toastification";
import Select2 from "../forms/Select2.vue";
export default {
    props: {

    },
    setup () { //https://pinia.vuejs.org/core-concepts/getters.html#passing-arguments-to-getters
        const store = useDatatableStore();
        const toast = useToast();
        return {
            store,
            toast
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                'group_curricula_ids': null
            }),
        }
    },
    mounted() {},
    methods: {
        enroleToCurricula(){
            axios.post('/curricula/enrol', {
                    'enrollment_list' : this.generateGroupProcessList()
                })
                .then(r => { this.feedbackSuccess(r); })
                .catch(e => { this.feedbackError(e); });
        },
        expelFromCurricula(){
            axios.delete('/curricula/expel', {
                    data: {
                        'expel_list' : this.generateGroupProcessList()
                    }
                })
                .then(r => { this.feedbackSuccess(r); })
                .catch(e => { this.feedbackError(e); });
        },
        generateGroupProcessList(){
            let ids = this.store.getDatatable('groups')?.selectedItems.map(x => x.id);
            let processList = [];
            //console.log(ids);
            if (typeof (ids) != 'undefined'){
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
        feedbackSuccess(r){
            if (r.data !== ''){
                this.successNotification(window.trans.global.group.enrol_success);
            }
        },
        feedbackError(e){
            this.errorNotification(window.trans.global.group.enrol_error);
            console.log(e.response);
        },
        deleteUser(){
            axios.delete('/groups/massDestroy',
                {
                    data: {
                        'ids': this.store.getDatatable('groups')?.selectedItems.map(x => x.id),
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
            this.toast.success(message/*, {
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: "button",
                icon: true,
                rtl: false
            }*/);
        },
        errorNotification(message) {
            this.toast.error(message/*, {
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: "button",
                icon: true,
                rtl: false
            }*/);
        },
    },
    components: {
        Select2
    },
}
</script>
