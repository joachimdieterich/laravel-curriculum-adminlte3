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
                        @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body"
                 style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">
                        {{ trans('global.task.fields.title') }} *
                    </label>
                    <input
                        type="text" id="title"
                        name="title"
                        class="form-control"
                        v-model="form.title"
                        placeholder="Title"
                        required
                        />
                     <p class="help-block"
                        v-if="form.errors.title"
                        v-text="form.errors.title[0]"></p>
                </div>

                <div class="form-group">
                    <label for="description">
                        {{ trans('global.task.fields.description') }}
                    </label>
                    <Editor
                        id="description"
                        name="description"
                        :placeholder="trans('global.task.fields.description')"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.description"
                    ></Editor>
                </div>

                <div class="form-group ">
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
                    ></VueDatePicker>
                    <p class="help-block"
                       v-if="form.errors.start_date"
                       v-text="form.errors.start_date[0]"></p>
                </div>

                <div class="form-group ">
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
                    ></VueDatePicker>
                    <p class="help-block"
                       v-if="form.errors.due_date"
                       v-text="form.errors.due_date[0]"></p>
                </div>
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="task-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="task-save"
                         class="btn btn-primary"
                         @click="submit(method)" >
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
        VueDatePicker
    },
    props: {
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
    },
    setup() { //use database store
        const globalStore = useGlobalStore();

        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/tasks',
            form: new Form({
                'id':'',
                'title': '',
                'description': '',
                'start_date': new Date(),
                'due_date': null,
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
            this.form.description = tinyMCE.get('description').getContent();
            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('task-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('task-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.description = this.$decodeHTMLEntities(params.description);
                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });

        this.form.start_date = new Date();
    },
}
</script>

