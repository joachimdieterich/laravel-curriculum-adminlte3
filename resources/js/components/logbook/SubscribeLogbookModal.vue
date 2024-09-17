<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.logbook.create') }}
                    </span>
                        <span v-if="method === 'patch'">
                        {{ trans('global.logbook.edit') }}
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
                        id="logbooks_subscription"
                        name="logbooks_subscription"
                        url="/logbooks"
                        model="logbook"
                        :selected="this.form.logbook_id"
                        @selectedValue="(id) => {
                            this.form.logbook_id = id;
                        }"
                    >
                    </Select2>
                </div>
                <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="logbook-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="logbook-save"
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
import Select2 from "../forms/Select2";
import {useGlobalStore} from "../../store/global";


export default {
    name: 'subscribe-logbook-modal',
    components:{
        Select2
    },
    props: {
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
        subscribable_type: '',
        subscribable_id: '',
    },
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
            url: '/logbookSubscriptions',
            form: new Form({
                'id': '',
                'logbook_id': '',
            }),
            search: '',
        }
    },
    methods: {
        submit() {
            axios.post(this.url, {
                'model_id': this.form.logbook_id,
                'subscribable_type': this.subscribable_type,
                'subscribable_id': this.subscribable_id
            })
            .then(r => {
                this.$eventHub.emit('logbook-subscription-added', r.data);
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
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined'){
                    this.form.populate(params);
                    if (this.form.id != ''){
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
