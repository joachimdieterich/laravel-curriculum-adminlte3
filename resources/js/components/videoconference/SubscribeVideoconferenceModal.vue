<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span>{{ trans('global.videoconference.enrol') }}</span>
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
                    class="card-body"
                    style="overflow-y: visible;"
                >
                    <Select2
                        id="videoconferences_subscription"
                        name="videoconferences_subscription"
                        url="/videoconferences"
                        model="videoconference"
                        :selected="this.form.videoconference_id"
                        @selectedValue="(id) => {
                            this.form.videoconference_id = id;
                        }"
                    />
                </div>
                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="videoconference-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="videoconference-save"
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
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'subscribe-videoconference-modal',
    components: {
        Select2,
    },
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
            form: new Form({
                id: '',
                videoconference_id: '',
            }),
            subscribable_id: '',
            subscribable_type: '',
        }
    },
    methods: {
        submit() {
            axios.post('/videoconferenceSubscriptions', {
                model_id: this.form.videoconference_id,
                subscribable_type: this.subscribable_type,
                subscribable_id: this.subscribable_id,
            })
            .then(r => {
                this.globalStore.closeModal(this.$options.name);
                this.$eventHub.emit('videoconference-subscription-added', r.data);
            })
            .catch(err => {
                console.log(err.response);
            });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params.reference;
                this.subscribable_id = state.modals[this.$options.name].params.subscribable_id;
                this.subscribable_type = state.modals[this.$options.name].params.subscribable_type;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                }
            }
        });
    },
}
</script>