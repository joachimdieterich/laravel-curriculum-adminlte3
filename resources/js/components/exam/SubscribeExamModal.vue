<template>
    <Modal
        model="exam"
        modalName="subscribe-exam-modal"
        title="global.exam.enrol"
        :form="form"
        :allow-overflow="true"
        :disable-save-button="!form.exam_id"
        :intercept-save="true"
        @save="submit()"
    >
        <template #general>
            <Select2 v-if="!group_id"
                id="exam-group"
                css="mb-3"
                model="group"
                url="/groups"
                @selectedValue="id => form.group_id = id[0]"
            />
            <Select2
                id="exams-subscription"
                :list="options"
                model="exam"
                option_label="name"
                @selectedValue="id => form.exam_id = id[0]"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    name: 'subscribe-exam-modal',
    components: {
        Modal,
        Select2,
    },
    props: {
        group_id: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                exam_id: null,
                group_id: null,
            }),
            options: [],
        }
    },
    methods: {
        submit() {
            let test = this.options.find(t => t.id == this.form.exam_id);

            this.sendCreateExamRequest(test.tool, test.id, test.nameLong, this.form.group_id);
        },
        async sendCreateExamRequest(tool, test_id, test_name, group_id) {
            await axios.post('/exams', {'tool': tool, 'test_id': test_id, 'test_name': test_name, 'group_id': group_id})
                .then(response => {
                    this.$eventHub.emit('exam-added', response.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                    this.$emit('failedNotification', e)
                });
        }
    },
    mounted() {
        this.form.group_id = this.group_id;

        axios.get('/tests')
            .then(response => {
                this.options = response.data
            })
            .catch(error => {
                console.warn(error.response);
            });
    },
}
</script>