<template>
    <Transition name="modal">
        <div v-if="show"
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
                                @click="$emit('close')">
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
                         @click="$emit('close')">
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


export default {
    components:{
        Select2
    },
    props: {
        show: {
            type: Boolean
        },
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            component_id: this._uid,
            method: 'post',
            url: '/logbookSubscriptions',
            form: new Form({
                'id': '',
                'logbook_id': '',
            }),
            search: '',
        }
    },
    watch: {
        params: function(newVal, oldVal) {
            this.form.reset();
            this.form.populate(newVal);
        },
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
    mounted() {},
}
</script>
