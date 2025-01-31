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
                        {{ trans('global.note.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.note.edit') }}
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

            <div class="modal-body">
                <Notes
                    :notable_type="this.form.notable_type"
                    :notable_id="this.form.notable_id"
                    :show_tabs="this.form.show_tabs"
                />
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="note-save"
                         class="btn btn-primary"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.close') }}
                     </button>
                </span>
            </div>
        </div>
    </div>
    </Transition>
</template>
<script>
import Notes from "./Notes.vue";
import Form from 'form-backend-validation';
import {useGlobalStore} from "../../store/global";

export default {
    name: 'note-modal',
    components: {
        Notes,
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
                'notable_type': false,
                'notable_id': false,
                'show_tabs': true,
            }),
        }
    },
    methods: {},
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    if (this.form.id !== '') {
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

