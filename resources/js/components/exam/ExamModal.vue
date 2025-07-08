<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.exam.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.exam.edit') }}
                        </span>
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

                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <Select2
                                id="exams_subscription"
                                name="exams_subscription"
                                :list="options"
                                model="exam"
                                option_label="name"
                                :selected="form.exam_id"
                                @selectedValue="(id) => {
                                    this.form.exam_id = id;
                                }"
                            />
                            <Select2
                                id="groups"
                                name="groups"
                                url="/groups"
                                model="group"
                                :multiple="false"
                                :selected="form.group_id"
                                    @selectedValue="(id) => {
                                    this.form.group_id = id[0];
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
                            @click="submit(method)"
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
    name: 'exam-modal',
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
            method: 'post',
            form: new Form({
                exam_id: '',
                group_id: '',
            }),
            options: [],
        }
    },
    methods: {
        submit() {
            let test = this.options.filter((t) => t.id == this.form.exam_id);
            this.SendCreateExamRequest(test[0].tool, test[0].id, test[0].nameLong, this.form.group_id);
        },
        async SendCreateExamRequest(tool, test_id, test_name, group_id) {
            console.log({'tool': tool, 'test_id': test_id, 'test_name': test_name, 'group_id': group_id})
            await axios.post('/exams', {'tool': tool, 'test_id': test_id, 'test_name': test_name, 'group_id': group_id})
                .then(response => {
                    this.globalStore.closeModal(this.$options.name);
                    this.$eventHub.emit('exam-added', response.data);
                })
                .catch(errors => {
                    this.$emit('failedNotification', errors);
                })
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    if (this.form.id !== '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });

        axios.get('/tests')
            .then(response => {
                this.options = response.data
            })
            .catch(errors => {
                if (errors.response.status !== 403) {
                    this.$emit('failedNotification', Vue.prototype.trans('global.exam.error_messages.get_tests'))
                }
            })
    },
}
</script>