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
                            {{ trans('global.period.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.period.edit') }}
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
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    v-model="form.title"
                                    :placeholder="trans('global.organizationType.fields.title') + ' *'"
                                    required
                                    />
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>
        
                            <VueDatePicker
                                v-model="form.date"
                                :range="{ partialRange: false }"
                                format="dd.MM.yyy HH:mm"
                                :teleport="true"
                                locale="de"
                                :placeholder="trans('global.selectDateRange') + ' *'"
                                :select-text="trans('global.ok')"
                                :cancel-text="trans('global.close')"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="organization-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="organization-save"
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
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {useGlobalStore} from "../../store/global";

export default {
    name: 'period-modal',
    components: {
        VueDatePicker,
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
            method: 'post',
            form: new Form({
                id:'',
                title: '',
                date: null,
                begin: new Date(),
                end: null,
            }),
        }
    },
    methods: {
        submit() {
            this.form.begin = this.form.date[0];
            this.form.end = this.form.date[1];

            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/periods', this.form)
                .then(r => {
                    this.$eventHub.emit('period-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/periods/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('period-updated', r.data);
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
                    this.form.date = [this.form.begin, this.form.end];
                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });

        const startDate = new Date();
        const endDate = new Date(new Date().setDate(startDate.getDate() + 7));
        this.form.date = [startDate, endDate];
    },
}
</script>