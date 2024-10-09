<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.plan.create') }}
                    </span>
                        <span v-if="method === 'patch'">
                        {{ trans('global.plan.edit') }}
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
                    <Select2
                        id="plans_subscription"
                        name="plans_subscription"
                        url="/plans"
                        model="plan"
                        :selected="this.form.plan_id"
                        @selectedValue="(id) => {
                            this.form.plan_id = id;
                        }"
                    >
                    </Select2>
                </div>
                <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="plan-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="plan-save"
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
    name: 'subscribe-plan-modal',
    components:{
        Select2
    },
    props: {},
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/planSubscriptions',
            form: new Form({
                'id': '',
                'plan_id': '',
            }),
            subscribable_type: '',
            subscribable_id: '',
            search: '',
        }
    },
    methods: {
        submit() {
            axios.post(this.url, {
                'model_id': this.form.plan_id,
                'subscribable_type': this.subscribable_type,
                'subscribable_id': this.subscribable_id
            })
            .then(r => {
                this.$eventHub.emit('plan-subscription-added', r.data);
                //console.log(r.data);
            })
            .catch(err => {
                console.log(err.response);
            });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (mutation.events.key === this.$options.name){
                const params = state.modals[this.$options.name].params.reference;
                this.subscribable_type = state.modals[this.$options.name].params.subscribable_type;
                this.subscribable_id = state.modals[this.$options.name].params.subscribable_id;
                this.form.reset();
                if (typeof (params) !== 'undefined'){
                    this.form.populate(params);
                    this.method = 'post';
                }
            }
        });
    },
}
</script>
