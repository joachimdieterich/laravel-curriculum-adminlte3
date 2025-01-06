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
                    <button type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="modal-body">
                <div class="form-group ">
                    <label for="title">
                        {{ trans('global.exercise.fields.title') }}
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        v-model="form.title"
                        :placeholder="trans('global.exercise.fields.title')"
                        required
                    />
                    <p class="help-block"
                       v-if="form.errors.title"
                       v-text="form.errors.title[0]"></p>
                    <div class="invalid-feedback">
                        {{ trans('global.invalid_form') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">
                        {{ trans('global.exercise.fields.description') }}
                    </label>
                    <Editor
                        :id="'description'+component_id"
                        :name="'description'+component_id"
                        :placeholder="trans('global.exercise.fields.description')"
                        class="form-control"
                        :init="tinyMCE"
                        :initial-value="form.description"
                    ></Editor>
                </div>
                <div class="form-group">
                    <input
                        type="number"
                        min="1"
                        id="recommended_iterations"
                        name="recommended_iterations"
                        class="form-control"
                        v-model.trim="form.recommended_iterations"
                        :placeholder="trans('global.exercise.fields.recommended_iterations')"
                        required
                    />
                    <div class="invalid-feedback">
                        {{ trans('global.invalid_form') }}
                    </div>
                </div>
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="exercise-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="exercise-save"
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
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'exercise-modal',
    components:{
        Editor
    },
    props: {
        map: {
            type: Object
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
            url: '/exercises',
            form: new Form({
                'id':'',
                'training_id':'',
                'title': '',
                'description': '',
                'recommended_iterations': '',
                'order_id': 0,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia"
                ],
                {
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id,
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
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('exercise-added', r.data.exercise);
                    this.globalStore?.closeModal(this.$options.name)
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            console.log('update');
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('exercise-updated', r.data.exercise);
                    this.globalStore?.closeModal(this.$options.name)
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

