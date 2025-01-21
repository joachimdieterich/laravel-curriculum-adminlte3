<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span>{{ trans('global.course.enrol') }}</span>
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
                        id="curricula"
                        name="curricula"
                        url="/curricula"
                        model="curriculum"
                        option_id="id"
                        option_label="title"
                        :selected="this.form.curriculum_id"
                        @selectedValue="(id) => {
                            this.form.curriculum_id = id;
                        }"
                    />
                </div>
                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="course-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="course-save"
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
    name: 'subscribe-course-modal',
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
                curriculum_id: '',
                enrollment_list: {},
            }),
            subscribable_id: null,
        }
    },
    methods: {
        submit() {
            axios.post('/curricula/enrol', {
                enrollment_list: {
                    0: {
                        group_id: this.subscribable_id,
                        curriculum_id: {
                            0: this.form.curriculum_id,
                        },
                    },
                },
            })
            .then(r => {
                this.$eventHub.emit('course-added', r.data);
                this.globalStore.closeModal(this.$options.name);
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
                this.subscribable_id = params.subscribable_id;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                }
            }
        });
    },
}
</script>