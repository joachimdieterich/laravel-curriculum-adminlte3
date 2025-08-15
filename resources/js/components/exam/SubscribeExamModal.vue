<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">{{ trans('global.exam.enrol') }}</span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :title="trans('global.close')"
                        @click="globalStore?.closeModal($options.name)"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div
                    class="modal-body"
                    style="overflow-y: visible;"
                >
                    <div class="card">
                        <div class="card-body">
                            <Select2
                                id="exams_subscription"
                                name="exams_subscription"
                                css="mb-0"
                                :list="options"
                                model="exam"
                                option_label="name"
                                :selected="form.exam_id"
                                @selectedValue="(id) => {
                                    this.form.exam_id = id;
                                }"
                            />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="exam-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="exam-save"
                            class="btn btn-primary ml-3"
                            @click="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'subscribe-exam-modal',
    components: {
        Select2,
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: '',
                exam_id: '',
            }),
            subscribable_type: '',
            subscribable_id: '',
            options: [],
        }
    },
    methods: {
        submit() {
            let test = this.options.filter((t) => t.id == this.form.exam_id);

            this.sendCreateExamRequest(test[0].tool, test[0].id, test[0].nameLong, this.subscribable_id);
        },
        async sendCreateExamRequest(tool, test_id, test_name, group_id) {
            console.log({'tool': tool, 'test_id': test_id, 'test_name': test_name, 'group_id': group_id})
            await axios.post('/exams', {'tool': tool, 'test_id': test_id, 'test_name': test_name, 'group_id': group_id})
                .then(response => {
                    this.$eventHub.emit('exam-added', response.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(errors => {
                    this.$emit('failedNotification', errors)
                });
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {

            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params.reference;
                this.subscribable_type = state.modals[this.$options.name].params.subscribable_type;
                this.subscribable_id = state.modals[this.$options.name].params.subscribable_id;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.method = 'post';
                }
            }
        });

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