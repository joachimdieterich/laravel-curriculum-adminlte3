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
                            {{ trans('global.tag.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.tag.edit') }}
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

                <div class="modal-body" style="overflow-y: visible;">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group"
                                 :class="form.errors.errors.name ? 'has-error' : ''"
                            >
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="form-control"
                                    v-model="form.name"
                                    :readonly="this.method == 'patch'"
                                    placeholder="Name *"
                                    required
                                />
                                <p class="error-block-wide" v-if="form.errors.errors.name" v-text="form.errors.errors.name[0]"></p>
                            </div>
                            <Select2 id="user_id"
                                     css="mb-0 mt-3"
                                     :label="trans('global.tag.type.title_singular')"
                                     model="type"
                                     url="/tags/type"
                                     :selected="form.type"
                                     :placeholder="trans('global.pleaseSelectOptional')"
                                     :read-only="method === 'patch'"
                                     @selectedValue="(type) => this.form.type = type[0]"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="tag-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="tag-save"
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
    name: 'tag-modal',
    components: {
        Select2,
    },
    props: {
        params: {
            type: Object,
        },
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
            form: new Form({
                id:'',
                name: '',
                type: null,
            }),
        }
    },
    methods: {
        submit() {
            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            this.form.post('/tags')
                .then(r => {
                    this.$eventHub.emit('tag-added', r);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        update() {
            axios.patch('/tags/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('tag-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
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