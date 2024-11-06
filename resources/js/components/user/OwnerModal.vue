<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.curriculum.edit_owner') }}
                </h3>
                <div class="card-tools">
                    <button
                        type="button"
                        class="btn btn-tool"
                        @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <Select2
                    id="owner_id"
                    name="owner_id"
                    url="/users"
                    model="user"
                    option_id="id"
                    option_label="text"
                    :selected="this.form.owner_id"
                    @selectedValue="(id) => {
                        this.form.owner_id = id;
                    }"
                >
                </Select2>
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="subject-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="subject-save"
                         class="btn btn-primary"
                         @click="submit()" >
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
import Select2 from "../forms/Select2.vue";

export default {
    name: 'owner-modal',
    components: {
        Select2
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
                'model_id':'',
                'model': '',
                'model_url': '',
                'owner_id': '',
            }),
        }
    },
    methods: {
        submit() {
            axios.patch(this.form.model_url + '/' + this.form.model_id + '/editOwner', this.form)
                .then(r => {
                    this.$eventHub.emit('owner-updated', r.data);
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
                }
            }
        });
    },
}
</script>
