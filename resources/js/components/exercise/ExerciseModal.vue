<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.exercise.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.exercise.edit') }}
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
                            v-model="form.title"
                            :placeholder="trans('global.exercise.fields.title') + ' *'"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <Editor
                            :id="'description'+component_id"
                            :name="'description'+component_id"
                            class="form-control"
                            :placeholder="trans('global.exercise.fields.description')"
                            :init="tinyMCE"
                            v-model="form.description"
                        />
                    </div>
                    <div class="form-group">
                        <label for="recommended_iterations">{{ trans('global.exercise.fields.recommended_iterations') }} *</label>
                        <input
                            type="number"
                            min="1"
                            id="recommended_iterations"
                            name="recommended_iterations"
                            class="form-control"
                            v-model.trim="form.recommended_iterations"
                            :placeholder="trans('global.exercise.fields.recommended_iterations_short')"
                            required
                        />
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="exercise-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="exercise-save"
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
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'exercise-modal',
    components: {
        Editor,
    },
    props: {
        map: {
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
                id:'',
                training_id:'',
                title: '',
                description: '',
                recommended_iterations: '',
                order_id: 0,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia autoresize"
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
            if (method == 'post') {
                this.add();
            } else {
                this.update();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/exercises', this.form)
                .then(response => {
                    this.$eventHub.emit('exercise-added', response.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/exercises/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('exercise-updated', response.data);
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
                    if (this.form.id !== '') {
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