<template>
    <div class="px-3">
        <div class="col-12 mb-3 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-university mx-1"></i>{{ config.key }}
                </h5>

                <button v-if="checkPermission('is_admin')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editConfig()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <div class="px-3 py-2 bg-white rounded-bottom-3">
                <strong>
                    <i class="fa fa-file-alt me-1"></i>{{ trans('global.config.fields.value') }}
                </strong>
                <p class="text-muted" v-html="config.value"></p>

                <hr>

                <strong>
                    <i class="fa fa-layer-group me-1"></i>{{ trans('global.config.fields.referenceable_type') }}
                </strong>
                <p class="text-muted">{{ config.referenceable_type }}</p>

                <hr>

                <strong>
                    <i class="fa fa-magnifying-glass me-1"></i>{{ trans('global.config.fields.referenceable_id') }}
                </strong>
                <p class="text-muted">{{ config.referenceable_id }}</p>

                <hr>

                <strong>
                    <i class="fa fa-magnifying-glass me-1"></i>{{ trans('global.config.fields.data_type') }}
                </strong>
                <p class="text-muted">{{ config.data_type }}</p>

                <small class="text-muted">{{ config.updated_at }}</small>
            </div>
        </div>

        <Teleport to="body">
            <ConfigModal/>
        </Teleport>
    </div>
</template>
<script>
import ConfigModal from "../config/ConfigModal.vue";

export default {
    name: "Config",
    components: { ConfigModal },
    props: {
        config: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            currentConfig: {},
        }
    },
    mounted() {
        this.$eventHub.on('config-updated', () => window.location.reload());
    },
    methods: {
        editConfig() {
            this.globalStore.showModal('config-modal', this.config);
        },
    },
}
</script>