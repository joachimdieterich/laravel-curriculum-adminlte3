<template>
    <Modal
        model="group"
        modalName="group-modal"
        :method="method"
        :processing="processing"
        :allow-overflow="true"
        @save="submit()"
    >
        <template #general>
            <div v-if="checkPermission('is_admin')"
                class="mb-3"
            >
                <label for="common_name">{{ trans('global.common_name') }}</label>
                <input
                    id="common_name"
                    name="common_name"
                    type="text"
                    class="form-control"
                    v-model="form.common_name"
                    readonly
                />
            </div>

            <div class="mb-3">
                <label for="title">{{ trans('global.group.fields.title') }} *</label>
                <input
                    id="title"
                    name="title"
                    type="text"
                    class="form-control"
                    v-model="form.title"
                    :placeholder="trans('global.title')"
                    required
                />
            </div>

            <Select2
                id="grade_id"
                name="grade_id"
                url="/grades"
                model="grade"
                css="mb-3"
                :label="trans('global.grade.title_singular') + ' *'"
                option_id="id"
                option_label="title"
                :selected="form.grade_id"
                @selectedValue="(id) => form.grade_id = id"
            />

            <Select2
                id="period_id"
                name="period_id"
                url="/periods"
                model="period"
                css="mb-3"
                :label="trans('global.period.title_singular') + ' *'"
                option_id="id"
                option_label="title"
                :selected="form.period_id"
                @selectedValue="(id) => form.period_id = id"
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
                :selected="form.organization_id"
                @selectedValue="(id) => form.organization_id = id"
            />
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'group-modal',
    components: {
        Modal,
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
            processing: false,
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
            this.processing = true;

            if (this.method === 'patch') {
                this.update();
            } else {
                this.add();
            }

        },
        add() {
            axios.post('/groups', this.form)
                .then(r => {
                    this.$eventHub.emit('group-added', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/groups/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('group-updated', r.data);
                    this.globalStore.closeModal(this.$options.name);
                })
                .catch(e => {
                    this.processing = false;
                    console.log(e.response);
                });
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                this.processing = false;
                this.form.reset();

                const params = state.modals[this.$options.name].params;
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.method = this.form.id ? 'patch' : 'post';
                }
            }
        });
    },
}
</script>