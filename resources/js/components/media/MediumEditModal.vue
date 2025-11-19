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

                <div
                    class="modal-body"
                    style="overflow: visible;"
                >
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group d-flex align-items-center">
                                <v-swatches
                                    style="height: 42px;"
                                    :swatches="$swatches"
                                    row-length="5"
                                    popover-y="top"
                                    v-model="form.color"
                                    show-fallback
                                    fallback-input-type="color"
                                />
                                <input
                                    id="medium-title"
                                    type="text"
                                    class="form-control ml-3"
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
                color: '#F2F4F5',
                description: '',
            }),
            subscribable_type: null,
            subscribable_id: null,
            additional_data: null,
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
                .then(async (response) => {
                    let subscription_data;
                    // only update color-attribute if it was actually changed
                    if (this.form.color !== (this.additional_data?.color ?? '#F2F4F5')) {
                        await axios.post('/mediumSubscriptions/updateAdditionalData', {
                            medium_id: this.form.id,
                            subscribable_type: this.subscribable_type,
                            subscribable_id: this.subscribable_id,
                            additional_data: {
                                ...this.additional_data,
                                color: this.form.color,
                            },
                        }).then(res => subscription_data = res.data);
                    }

                    this.$eventHub.emit('medium-updated', {
                        medium: response.data,
                        additional_data: subscription_data,
                    });
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

            const subscription = state.modals[this.$options.name].params;
            const medium = subscription.medium;

            this.subscribable_type = subscription.subscribable_type;
            this.subscribable_id = subscription.subscribable_id;
            this.additional_data = subscription.additional_data;
            
            this.form.reset();
            if (medium !== undefined && medium !== null) this.form.populate(medium);
            if (this.additional_data) this.form.color = this.additional_data.color ?? '#F2F4F5';
        });
    },
};
</script>