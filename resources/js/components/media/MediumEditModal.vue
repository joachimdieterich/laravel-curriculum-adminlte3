<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            style="z-index: 10000000 !important;"
            @mouseup.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <span class="card-title">{{ trans('global.medium.edit') }}</span>
                    <button
                        type="button"
                        class="btn btn-icon text-secondary"
                        :title="trans('global.close')"
                        @click="globalStore?.closeModal($options.name)"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="medium-title">{{ trans('global.title') }}</label>
                                <input
                                    id="medium-title"
                                    type="text"
                                    class="form-control"
                                    :placeholder="trans('global.medium.title_singular') + ' ' + trans('global.title')"
                                    v-model.trim="form.title"
                                />
                            </div>

                            <div class="form-group">
                                <label for="medium-description">{{ trans('global.description') }}</label>
                                <textarea
                                    id="medium-description"
                                    name="medium-description"
                                    class="form-control"
                                    :placeholder="trans('global.medium.title_singular') + ' ' + trans('global.description')"
                                    v-model.trim="form.description"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="medium-edit-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="medium-edit-save"
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
import {useGlobalStore} from "../../store/global.js";

export default {
    name: 'medium-edit-modal',
    data() {
        return {
            form: new Form({
                id: null,
                title: '',
                description: '',
            }),
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    methods: {
        submit() {
            axios.patch('/media/' + this.form.id, this.form)
                .then(response => {
                    this.$eventHub.emit('medium-updated', response.data);
                    this.globalStore?.closeModal(this.$options.name);
                })
                .catch(error => {
                    console.log(error);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (!state.modals[this.$options.name].show) return;

            const params = state.modals[this.$options.name].params;
            
            this.form.reset();
            if (params !== undefined && params !== null) this.form.populate(params);
        });
    },
};
</script>