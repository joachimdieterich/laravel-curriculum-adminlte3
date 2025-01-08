<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.trainings.create') }}
                        </span>
                        <span v-else>
                            {{ trans('global.trainings.edit') }}
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

                <div class="modal-body">

                </div>

                <div class="modal-footer">
                    <span class="pull-right">
                        <button
                            id="training-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="training-save"
                            class="btn btn-primary"
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
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    name: 'training-modal',
    props: {
        plan: {
            type: Object,
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
            form: new Form({
                id: '',
                title: '',
                description: '',
                plan_id: this.plan.id,
                date: null,
                begin: '',
                end: '',
            }),
        }
    },
    methods: {
        submit() {
            if (this.method == 'post') {
                this.add();
            } else {
                this.update();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/trainings', this.form)
                .then(response => {
                    this.$eventHub.emit('training-added', response.data);
                })
                .catch(error => {
                    console.log(error)
                });
        },
        update() {
            axios.patch('/trainings/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('training-updated', response.data);
                })
                .catch(error => {
                    console.log(error)
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
                    this.form.date = [new Date(this.form.begin) ?? '', new Date(this.form.end) ?? ''];
                    this.form.description = this.$decodeHTMLEntities(params.description);

                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });
    },
    components: {
        VueDatePicker,
    },
}
</script>