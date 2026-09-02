<template>
    <Modal
        model="logbookEntrySubject"
        modalName="logbook-entry-subject-modal"
        title="global.logbookEntry.subject"
        :processing="processing"
        :allow-overflow="true"
        @save="submit()"
    >
        <template #general>
            <Select2
                :id="'subject_' + component_id "
                :name="'subject_' + component_id "
                option_id="id"
                url="/subjects"
                model="subject"
                :selected="form.subject_id"
                @selectedValue="(id) => form.subject_id = id"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'logbook-entry-subject-modal',
    components: {
        Modal,
        Select2,
    },
    props: {},
    setup() {
        return {
            globalStore: useGlobalStore(),
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            processing: false,
            form: new Form({
                id: '',
                subject_id:'',
            }),
        }
    },
    methods: {
        submit() {
            this.processing = true;

            axios.patch('/logbookEntries/' + this.form.id + '/setSubject', this.form)
                .then(response => {
                    this.globalStore.closeModal(this.$options.name);
                    this.$eventHub.emit('update-subject-badge', {
                        entry_id: this.form.id,
                        subject: response.data,
                    });
                })
                .catch(e => {
                    this.processing = false;
                    console.log(e.response);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                this.processing = false;
                this.form.reset();

                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                }
            }
        });
    },
}
</script>