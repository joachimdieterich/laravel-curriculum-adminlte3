<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.config.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.config.edit') }}
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

                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="form-group "
                        :class="form.errors.key ? 'has-error' : ''">
                        <label for="key">
                            {{ trans('global.config.fields.key') }}
                        </label>
                        <input
                            type="text"
                            id="key"
                            name="key"
                            class="form-control float"
                            v-model="form.key"
                            placeholder="Title"
                            required
                            />
                         <p class="help-block" v-if="form.errors.key" v-text="form.errors.key[0]"></p>
                    </div>
                    <div class="form-group "
                         :class="form.errors.value ? 'has-error' : ''"
                    >
                        <label for="value">
                            {{ trans('global.config.fields.value') }} *
                        </label>
                        <textarea
                            id="value"
                            name="value"
                            class="form-control description my-editor "
                            v-model="form.value"
                        ></textarea>
                        <p class="help-block" v-if="form.errors.value" v-text="form.errors.value[0]"></p>
                    </div>
                    <div class="form-group ">
                        <label for="referenceable_type">
                            {{ trans('global.config.fields.referenceable_type') }}
                        </label>
                        <input
                            type="text"
                            id="referenceable_type"
                            name="referenceable_type"
                            class="form-control"
                            required
                            v-model.trim="form.referenceable_type"
                        />
                        <p class="help-block" v-if="form.errors?.referenceable_type" v-text="form.errors?.referenceable_type[0]"></p>
                    </div>
                    <div class="form-group ">
                        <label for="referenceable_id">
                            {{ trans('global.config.fields.referenceable_id') }}
                        </label>
                        <input
                            type="number"
                            id="referenceable_id"
                            name="referenceable_id"
                            class="form-control"
                            required
                            v-model.trim="form.referenceable_id"
                        />
                        <p class="help-block" v-if="form.errors?.referenceable_id" v-text="form.errors?.referenceable_id[0]"></p>
                    </div>

                    <div class="form-group ">
                        <label for="data_type">
                            {{ trans('global.config.fields.data_type') }}
                        </label>
                        <input
                            type="text"
                            id="data_type"
                            name="data_type"
                            class="form-control"
                            required
                            v-model.trim="form.data_type"
                        />
                        <p class="help-block" v-if="form.errors?.data_type" v-text="form.errors?.data_type[0]"></p>
                    </div>
                </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="config-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="config-save"
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
import {useGlobalStore} from "../../store/global";

export default {
    name: 'config-modal',
    components:{},
    props: {},
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
            url: '/configs',
            form: new Form({
                'id':'',
                'key': '',
                'value': '',
                'referenceable_type': '',
                'referenceable_id': '',
                'data_type': '',
            }),
            search: '',
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
                    this.$eventHub.emit('config-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('config-updated', r.data);
                })
                .catch(error => { // Handle the error returned from our request
                    console.log(error);
                });
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
                    if (this.form.id !== ''){
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
