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
                            {{ trans('global.group.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.group.edit') }}
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

                <div
                    class="modal-body"
                    style="overflow-y: visible;"
                >
                    <div class="card">
                        <div class="card-body">
                            <div
                                v-permission="'is_admin'"
                                class="form-group"
                            >
                                <label for="common_name">{{ trans('global.common_name') }}</label>
                                <input
                                    id="common_name"
                                    type="text"
                                    name="common_name"
                                    class="form-control"
                                    v-model="form.common_name"
                                    readonly
                                />
                            </div>
        
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <label for="title">{{ trans('global.group.fields.title') }} *</label>
                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    v-model="form.title"
                                    placeholder="Title"
                                    required
                                />
                                <p class="help-block"
                                    v-if="form.errors.title"
                                    v-text="form.errors.title[0]"
                                ></p>
                            </div>
        
                            <Select2
                                id="grade_id"
                                name="grade_id"
                                url="/grades"
                                model="grade"
                                :label="trans('global.grade.title_singular') + ' *'"
                                option_id="id"
                                option_label="title"
                                :selected="this.form.grade_id"
                                @selectedValue="(id) => {
                                    this.form.grade_id = id;
                                }"
                            />
        
                            <Select2
                                id="period_id"
                                name="period_id"
                                url="/periods"
                                model="period"
                                :label="trans('global.period.title_singular') + ' *'"
                                option_id="id"
                                option_label="title"
                                :selected="this.form.period_id"
                                @selectedValue="(id) => {
                                    this.form.period_id = id;
                                }"
                            />
        
                            <Select2
                                id="organization_id"
                                name="organization_id"
                                url="/organizations"
                                model="organization"
                                css="mb-0"
                                :label="trans('global.organization.title_singular') + ' *'"
                                option_id="id"
                                option_label="title"
                                :selected="this.form.organization_id"
                                @selectedValue="(id) => {
                                    this.form.organization_id = id;
                                }"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="group-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="group-save"
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
    name: 'group-modal',
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
            method: 'post',
            form: new Form({
                id:'',
                title: '',
                common_name:'',
                grade_id: '',
                period_id: '',
                organization_id: '',
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
            axios.post('/groups', this.form)
                .then(r => {
                    this.$eventHub.emit('group-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/groups/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('group-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        }
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