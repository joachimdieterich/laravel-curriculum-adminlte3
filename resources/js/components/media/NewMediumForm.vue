<!-- Placeholder | replace with MediumForm.vue if functional and tested -->
<template>
    <div v-if="subscribable_id || allow_fallback_on_create"
        v-permission="'external_medium_create'"
        class="d-flex align-items-center ml-auto"
    >
        <button v-if="previewMedium && media_subscriptions.length < 2"
            type="button"
            class="btn btn-icon mr-2"
            @click="deleteSubscription()"
        >
            <i class="fa fa-trash text-danger"></i>
        </button>
        <div
            class="btn-group"
            style="max-height: 100px;"
        >
            <button v-if="previewMedium"
                type="button"
                class="btn btn-default"
            >
                <span class="position-relative d-flex align-items-center h-100">
                    <img
                        :src="'/media/' + previewMedium.id + '?preview=true'"
                        :alt="previewMedium.name ?? previewMedium.title ?? 'preview'"
                        class="img-size-64 h-100"
                        style="border-radius: 10px; object-fit: contain;"
                    />
                    <span v-if="media_subscriptions.length > 1"
                        class="position-absolute d-flex align-items-center justify-content-center text-black bg-white rounded-pill"
                        style="right: -12px; height: 24px; width: 24px; box-shadow: 0px 0px 3px black;"
                    >
                        <i
                            class="fa fa-plus"
                            style="font-size: 14px;"
                        >
                            {{ media_subscriptions.length - 1 }}
                        </i>
                    </span>
                </span>
            </button>
            <button
                type="button"
                class="btn btn-primary d-flex align-items-center pl-0"
                @click="openMediumModal()"
            >
                <i class="fa fa-cloud-upload px-1 mx-2"></i>
                <div style="width: min-content;">
                    {{ multiple ? trans('global.medium.add') : trans('global.medium.title_singular') }}
                </div>
            </button>
        </div>
    </div>
</template>
<script>
import {useGlobalStore} from "../../store/global";

export default {
    emits: ['add', 'delete'],
    props: {
        subscribable_id: {
            type: Number,
            default: null,
            description: 'ID is not required if fallback is enabled',
        },
        subscribable_type: {
            type: String,
            default: null,
            required: true,
        },
        allow_fallback_on_create: {
            type: Boolean,
            default: false,
            description: 'uses fallback-scheme to create subscriptions on non-existing ressources',
        },
        media_subscriptions: {
            type: Array,
            default: [],
            description: 'either a list of subscriptions...'
        },
        medium_id: {
            type: Number,
            default: null,
            description: '...or a single ID can be given',
        },
        multiple: {
            type: Boolean,
            default: false,
            description: 'allow subscribing multiple media to the ressource',
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            previewMedium: null,
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    mounted() {
        this.previewMedium = this.medium_id !== null
            ? { id: this.medium_id }
            : this.media_subscriptions[0]?.medium;

        this.$eventHub.on('new-media-subscriptions', (data) => {
            if (data?.id !== this.component_id) return;

            if (this.previewMedium == null) this.previewMedium = data.selectedMedia[0].medium ?? data.selectedMedia[0];
            this.$emit('add', data.selectedMedia);
        });
    },
    methods: {
        openMediumModal() {
            const type_fallback = this.subscribable_type + 'Create';

            this.globalStore?.showModal('medium-modal', {
                subscribable_id: this.subscribable_id ?? this.$userId,
                subscribable_type: this.subscribable_id ? this.subscribable_type : type_fallback,
                subscribeSelected: true,
                public: true,
                callback: 'new-media-subscriptions',
                callbackId: this.component_id,
            });
        },
        deleteSubscription() {
            const type_fallback = this.subscribable_type + 'Create';

            axios.post('/mediumSubscriptions/destroy', {
                medium_id: this.previewMedium.id,
                subscribable_id: this.subscribable_id ?? this.$userId,
                subscribable_type: this.subscribable_id ? this.subscribable_type : type_fallback,
                additional_data: true, // hack to skip setting medium_id of model to null
            }).then(() => {
                this.$emit('delete', this.previewMedium.id);
                this.previewMedium = null;
            });
        },
    },
}
</script>