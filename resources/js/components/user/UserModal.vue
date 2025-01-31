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
                            {{ trans('global.user.create') }}
                        </span>
                            <span v-if="method === 'patch'">
                            {{ trans('global.user.edit') }}
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
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="form-group"
                                :class="form.errors.username ? 'has-error' : ''"
                            >
                                <label for="username">{{ trans('global.user.fields.username') }} *</label>
                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    class="form-control"
                                    v-model="form.username"
                                    :placeholder="trans('global.user.fields.username')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.username" v-text="form.errors.username[0]"></p>
                            </div>

                            <div
                                class="form-group"
                                :class="form.errors.firstname ? 'has-error' : ''"
                            >
                                <label for="firstname">{{ trans('global.user.fields.firstname') }} *</label>
                                <input
                                    id="firstname"
                                    type="text"
                                    name="firstname"
                                    class="form-control"
                                    v-model="form.firstname"
                                    :placeholder="trans('global.user.fields.firstname')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.firstname" v-text="form.errors.firstname[0]"></p>
                            </div>

                            <div
                                class="form-group"
                                :class="form.errors.lastname ? 'has-error' : ''"
                            >
                                <label for="lastname">{{ trans('global.user.fields.lastname') }} *</label>
                                <input
                                    id="lastname"
                                    type="text"
                                    name="lastname"
                                    class="form-control"
                                    v-model="form.lastname"
                                    :placeholder="trans('global.user.fields.lastname')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.lastname" v-text="form.errors.lastname[0]"></p>
                            </div>

                            <div
                                class="form-group"
                                :class="form.errors.email ? 'has-error' : ''"
                            >
                                <label for="email">{{ trans('global.user.fields.email') }} *</label>
                                <input
                                    id="email"
                                    type="text"
                                    name="email"
                                    class="form-control"
                                    v-model="form.email"
                                    :placeholder="trans('global.user.fields.email')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.email" v-text="form.errors.email[0]"></p>
                            </div>

                            <div v-if="method == 'post'"
                                class="form-group"
                                :class="form.errors.password ? 'has-error' : ''"
                            >
                                <label for="email">{{ trans('global.user.fields.password') }} *</label>
                                <input
                                    id="password"
                                    type="text"
                                    name="password"
                                    class="form-control"
                                    v-model="form.password"
                                    :placeholder="trans('global.user.fields.password')"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.password" v-text="form.errors.password[0]"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="user-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="user-save"
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

export default {
    name: 'user-modal',
    components: {},
    props: {
        params: {
            type: Object,
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
                username: '',
                firstname: '',
                lastname: '',
                email: '',
                password: '',
            }),
        }
    },
    methods: {
        submit() {
            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/users', this.form)
                .then(r => {
                    this.$eventHub.emit('user-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update(){
            axios.patch('/users/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('user-updated', r.data);
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