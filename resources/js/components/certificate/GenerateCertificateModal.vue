<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ trans('global.certificate.create') }}
                    </h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div
                    class="modal-body"
                    style="overflow-y: visible;"
                >
                    <div class="card">
                        <div class="card-body">
                            <Select2 v-if="list"
                                id="certificate_list"
                                name="certificate_list"
                                :list="list"
                                model="certificate"
                                @selectedValue="(id) => {
                                    this.form.certificate_id = id[0];
                                }"
                            />
                            <Select2 v-else-if="form.curriculum_id"
                                id="certificate_id"
                                name="certificate_id"
                                :url="'/curricula/' + form.curriculum_id + '/certificates'"
                                model="certificate"
                                @selectedValue="(id) => {
                                    this.form.certificate_id = id;
                                }"
                            />
                            <div class="form-group">
                                <label for="title">{{ trans('global.date') }} *</label>
                                <input
                                    id="date"
                                    type="text"
                                    name="date"
                                    v-model="form.date"
                                    class="form-control"
                                    placeholder="01.01.2020"
                                    required
                                />
                            </div>
                            <Switch
                                id="global"
                                :label="trans('global.one_file')"
                                v-model:checked="form.oneFile"
                            />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="certificate-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)">
                            {{ trans('global.cancel') }}
                        </button>
                        <button v-if="!download_url"
                            id="btn_generate"
                            class="btn btn-primary ml-3"
                            :disabled="form.certificate_id === null"
                            @click="submit()"
                        >
                            <div v-if="download_url === false"
                                class="text-center text-white"
                            >
                                <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                                <span class="sr-only">Loading...</span>
                            </div>
                            <span v-else> {{ trans('global.generate') }} </span>
                        </button>

                        <a v-if="download_url"
                            id="btn_download"
                            class="btn btn-primary hidden ml-3"
                            :href="download_url"
                            target="_blank"
                            @click="download_url = null; globalStore.closeModal($options.name)"
                        >
                            <i class="fa fa-download"></i>
                            {{ trans('global.downloadFile') }}
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import {useDatatableStore} from "../../store/datatables";
import {useGlobalStore} from "../../store/global";
import {useToast} from 'vue-toastification';
import Switch from "../forms/Switch.vue";

export default {
    name: 'generate-certificate-modal',
    components:{
        Switch,
        Select2,
    },
    setup() {
        const store = useDatatableStore();
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            store,
            globalStore,
            toast,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                certificate_id: null,
                curriculum_id: null,
                user_ids: [],
                date: new Date().toLocaleDateString("de-DE", { dateStyle: "medium" }),
                oneFile: false,
            }),
            list: null,
            download_url: null,
        }
    },
    methods: {
        submit() {
            this.download_url = false;
            if (this.form.user_ids.length === 0) this.form.user_ids = this.store.getSelectedIds('curriculum-user-datatable');

            axios.post('/certificates/generate', this.form)
                .then(r => {
                    this.download_url = r.data.message;
                })
                .catch(e => {
                    this.toast.error(this.errorMessage(e));
                    console.log(e.response);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {

            const params = state.modals[this.$options.name].params;
            this.form.reset();
            if (typeof (params) !== 'undefined') {
                if (params.certificates) this.list = params.certificates;
                this.form.populate(params);
            }
        });
    },
}
</script>