<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.task.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.task.edit') }}
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
                            <div class="form-group">
                                <label for="title">
                                    {{ trans('global.title') }} *
                                </label>
                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    v-model="form.title"
                                    :placeholder="trans('global.title')"
                                    required
                                />
                            </div>
        
                            <div class="form-group">
                                <label for="description">
                                    {{ trans('global.description') }}
                                </label>
                                <Editor
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    :init="tinyMCE"
                                    v-model="form.description"
                                />
                            </div>
        
                            <div class="form-group">
                                <label for="start_date">
                                    {{ trans('global.task.fields.start_date') }}
                                </label>
                                <VueDatePicker
                                    v-model="form.start_date"
                                    :teleport="true"
                                    locale="de"
                                    format="dd.MM.yyy HH:mm"
                                    :select-text="trans('global.ok')"
                                    :cancel-text="trans('global.close')"
                                />
                            </div>
        
                            <div>
                                <label for="due_date">
                                    {{ trans('global.task.fields.due_date') }}
                                </label>
                                <VueDatePicker
                                    v-model="form.due_date"
                                    :teleport="true"
                                    locale="de"
                                    format="dd.MM.yyy HH:mm"
                                    :select-text="trans('global.ok')"
                                    :cancel-text="trans('global.close')"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="task-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="task-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.title"
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
import Editor from '@tinymce/tinymce-vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {useGlobalStore} from "../../store/global";

export default {
    name: 'task-modal',
    components: {
        Editor,
        VueDatePicker,
    },
    props: {
        params: {
            type: Object,
            default: null,
        },
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
                id:'',
                title: '',
                description: '',
                start_date: new Date(),
                due_date: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                }
            ),
        }
    },
    methods: {
        submit(method) {
            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/tasks', this.form)
                .then(r => {
                    this.$eventHub.emit('task-added', r.data);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        update() {
            axios.patch('/tasks/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('task-updated', r.data);
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });
    },
}
</script>