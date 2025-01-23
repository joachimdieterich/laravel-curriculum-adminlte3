<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.training.create') }}
                        </span>
                        <span v-else>
                            {{ trans('global.training.edit') }}
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
                    <div class="form-group">
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            v-model.trim="form.title"
                            :placeholder="trans('global.training.fields.title') + ' *'"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <Editor
                            :id="'description' + component_id"
                            :name="'description' + component_id"
                            class="form-control"
                            :init="tinyMCE"
                            v-model.trim="form.description"
                        />
                    </div>

                    <div class="form-group">
                        <VueDatePicker
                            v-model="form.date"
                            :teleport="true"
                            format="dd.MM.yyy HH:mm"
                            locale="de"
                            range
                            :partialRange="false"
                            :placeholder="trans('global.selectDateRange')"
                            :select-text="trans('global.ok')"
                            :cancel-text="trans('global.close')"
                        />
                    </div>
                </div>

                <div class="modal-footer">
                    <span class="pull-right">
                        <button
                            id="training-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="training-save"
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
import {useGlobalStore} from "../../store/global";
import Editor from '@tinymce/tinymce-vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    name: 'training-modal',
    props: {
        plan: {
            type: Object,
        }
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
                id: '',
                title: '',
                description: '',
                date: null,
                begin: '',
                end: '',
                plan_id: this.plan.id,
                subscribable_id: null,
                subscribable_type: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link lists table code autoresize"
                ],
                {
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id,
                },
                "bold underline italic | alignleft aligncenter alignright | table",
                "bullist numlist outdent indent | mathjax link code",
            ),
        }
    },
    methods: {
        submit() {
            if (this.method == 'post') {
                this.add();
            } else {
                this.update();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/trainings', this.form)
                .then(response => {
                    console.log('successfull response');
                    
                    this.$eventHub.emit('training-added', {
                        training: response.data,
                        id: this.form.subscribable_id,
                    });
                })
                .catch(error => {
                    console.log(error)
                });
        },
        update() {
            axios.patch('/trainings/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('training-updated', {
                        training: response.data,
                        id: this.form.subscribable_id,
                    });
                })
                .catch(error => {
                    console.log(error)
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
                    this.form.date = [new Date(this.form.begin) ?? '', new Date(this.form.end) ?? ''];
                    this.form.description = this.$decodeHTMLEntities(params.description);

                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });
    },
    components: {
        VueDatePicker,
        Editor,
    },
}
</script>