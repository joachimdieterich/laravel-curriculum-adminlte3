<template>
    <Modal
        model="certificate"
        modalName="generate-certificate-modal"
        :form="form"
        :allow-overflow="true"
        save-label="global.generate"
        :show-save-button="download_url === null"
        :disable-save-button="!form.certificate_id || !form.date"
        :interceptSave="true"
        @opened="params => preProcessing(params)"
        @save="submit()"
    >
        <template #general>
            <Select2 v-if="list"
                id="certificate-list"
                :list="list"
                model="certificate"
                @selectedValue="id => form.certificate_id = id[0]"
            />
            <Select2 v-else-if="form.curriculum_id"
                id="certificate-list"
                :url="'/curricula/' + form.curriculum_id + '/certificates'"
                model="certificate"
                @selectedValue="id => form.certificate_id = id"
            />
            <div class="my-3">
                <label for="dp-input-generate-certificate-date">{{ trans('global.date') }} *</label>
                <VueDatePicker
                    uid="generate-certificate-date"
                    v-model="form.date"
                    :teleport="true"
                    format="dd.MM.yyy"
                    locale="de"
                    :placeholder="trans('global.date')"
                    :select-text="trans('global.ok')"
                    :cancel-text="trans('global.close')"
                />
            </div>
            <Switch
                id="generate-certificate-single-file"
                label="global.one_file"
                v-model="form.oneFile"
            />
            <a v-if="download_url"
                ref="download"
                :href="download_url"
                class="btn btn-primary my-2 float-end"
                download
            >
                {{ trans('global.downloadFile') }}
            </a>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import {useDatatableStore} from "../../store/datatables";
import '@vuepic/vue-datepicker/dist/main.css';
import Switch from "../forms/Switch.vue";

export default {
    name: 'generate-certificate-modal',
    components:{
        Modal,
        Switch,
        Select2,
        VueDatePicker,
    },
    setup() {
        return { store: useDatatableStore() }
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                certificate_id: null,
                curriculum_id: null,
                user_ids: [],
                date: Date.now(),
                oneFile: false,
            }),
            list: null,
            download_url: null,
        }
    },
    methods: {
        preProcessing(params) {
            this.list = params.certificates;
        },
        submit() {
            if (this.form.user_ids.length === 0) this.form.user_ids = this.store.getSelectedIds('curriculum-user-datatable');

            axios.post('/certificates/generate', this.form)
                .then(r => {
                    this.download_url = r.data.message;
                    window.open(this.download_url, '_blank');
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>