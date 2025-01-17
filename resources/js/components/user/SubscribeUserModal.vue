<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
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
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="user-save"
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
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
    },
    setup() { //https://pinia.vuejs.org/core-concepts/getters.html#passing-arguments-to-getters
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/groups/enrol',
            form: new Form({
                'id': '',
                'user_id': '',
            }),
            search: '',
        }
    },
    methods: {
        submit(method) {
            if (method === 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url,
                {
                    'enrollment_list' : {
                        0: {
                            'group_id' : this.form.id,
                            'user_id': {
                                0 : this.form.user_id
                            }
                        }
                    }
                })
                .then(r => {
                    console.log(r);
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
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.method = 'post';
                }
            }
        });
    },
}
</script>