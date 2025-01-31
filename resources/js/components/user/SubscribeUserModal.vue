<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span>{{ trans('global.user.enrol') }}</span>
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
                <div
                    class="modal-body"
                    style="overflow-y: visible;"
                >
                    <Select2
                        id="users_subscription"
                        name="users_subscription"
                        url="/users"
                        model="user"
                        option_id="id"
                        option_label="text"
                        :selected="this.form.user_id"
                        @selectedValue="(id) => {
                            this.form.user_id = id;
                        }"
                    />
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
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'subscribe-user-modal',
    components: {
        Select2,
    },
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
            form: new Form({
                id: '',
                user_id: '',
            }),
            subscribable_id: null,
        }
    },
    methods: {
        submit() {
            axios.post('/groups/enrol', {
                enrollment_list: {
                    0: {
                        group_id: this.subscribable_id,
                        user_id: {
                            0: this.form.user_id,
                        },
                    },
                },
            })
                .then(r => {
                    this.globalStore.closeModal(this.$options.name);
                    this.$eventHub.emit('user-added', r.data);
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
                this.subscribable_id = state.modals[this.$options.name].params.subscribable_id;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                }
            }
        });
    },
}
</script>