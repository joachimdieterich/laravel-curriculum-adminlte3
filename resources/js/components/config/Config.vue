<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-university mr-1"></i>
                            {{ this.config.key }}
                        </h5>
                    </div>
                    <div
                        v-permission="'config_edit'"
                        class="card-tools pr-2">
                        <a  @click="editConfig(config)">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>

                </div>

                <div class="card-body">

                    <strong><i class="fa fa-file-alt mr-1"></i>
                        {{ trans('global.config.fields.value') }}
                    </strong>
                    <p class="text-muted"
                       v-dompurify-html="config.value"></p>
                    <hr>

                    <strong>
                        <i class="fas fa-layer-group mr-1"></i>
                        {{ trans('global.config.fields.referenceable_type') }}
                    </strong>
                    <p class="text-muted">
                        {{ config.referenceable_type }}
                    </p>
                    <hr>
                    <strong>
                        <i class="fas fa-magnifying-glass mr-1"></i>
                        {{ trans('global.config.fields.referenceable_id') }}
                    </strong>
                    <p class="text-muted">
                        {{ config.referenceable_id }}
                    </p>
                    <hr>
                    <strong>
                        <i class="fas fa-magnifying-glass mr-1"></i>
                        {{ trans('global.config.fields.data_type') }}
                    </strong>
                    <p class="text-muted">
                        {{ config.data_type }}
                    </p>

                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ config.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <ConfigModal></ConfigModal>
        </Teleport>
    </div>
</template>

<script>
import ConfigModal from "../config/ConfigModal.vue";

export default {
    name: "Config",
    components:{
        ConfigModal
    },
    props: {
        config: {
            default: null
        },
        status_definitions: {
            default: null
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            currentConfig: {},
        }
    },
    mounted() {
        this.$eventHub.on('config-updated', (config) => {
            this.globalStore?.closeModal('config-modal');
            window.location.reload();
        });
    },
    methods: {
        editConfig(config){
            this.globalStore?.showModal('config-modal',config);
        },
    }
}
</script>
