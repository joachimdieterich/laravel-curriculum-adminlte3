<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-university mr-1"></i>
                            {{ variantDefinition.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_type_edit'"
                        class="card-tools pr-2">
                        <a  @click="editVariantDefinition(variantDefinition)">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <strong>
                        <i class="fas fa-link mr-1"></i>
                        {{ trans('global.variantDefinition.fields.color') }}
                    </strong>
                    <p class="text-muted">
                        {{ variantDefinition.color }}
                    </p>
                    <hr>
                    <strong>
                        <i :class="variantDefinition.css_icon + 'mr-1'"></i>
                        {{ trans('global.variantDefinition.fields.css_icon') }}
                    </strong>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ variantDefinition.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <VariantDefinitionModal></VariantDefinitionModal>
        </Teleport>
    </div>
</template>

<script>
import VariantDefinitionModal from "../variantDefinition/VariantDefinitionModal.vue";
import {useGlobalStore} from "../../store/global";


export default {
    name: "VariantDefinition",
    components:{
        VariantDefinitionModal
    },
    props: {
        variantDefinition: {
            default: null
        },
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            componentId: this.$.uid,
            currentVariantDefinition: {},
        }
    },
    mounted() {},
    methods: {
        editVariantDefinition(variantDefinition){
            this.currentVariantDefinition = variantDefinition;
            this.globalStore?.showModal('variant-definition-modal', this.currentVariantDefinition);

        },
    }

}
</script>
