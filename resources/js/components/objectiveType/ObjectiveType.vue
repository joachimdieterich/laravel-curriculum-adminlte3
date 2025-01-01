<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-university mr-1"></i>
                            {{ objectiveType.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'objectivetype_edit'"
                        class="card-tools pr-2">
                        <a  @click="editObjectiveType(objectiveType)">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body"></div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ objectiveType.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <ObjectiveTypeModal></ObjectiveTypeModal>
        </Teleport>
    </div>
</template>

<script>
import ObjectiveTypeModal from "../objectiveType/ObjectiveTypeModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: "ObjectiveType",
    components:{
        ObjectiveTypeModal
    },
    props: {
        objectiveType: {
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
            currentObjectiveType: {},
        }
    },
    mounted() {},
    methods: {
        editObjectiveType(objectiveType){
            this.currentObjectiveType = objectiveType;
            this.globalStore?.showModal('objective-type-modal', this.currentObjectiveType);

        },
    }

}
</script>
