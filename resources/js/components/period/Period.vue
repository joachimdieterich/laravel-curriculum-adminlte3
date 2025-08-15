<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fa fa-history mr-1"></i>
                            {{ this.currentPeriod.title }}
                        </h5>
                    </div>
                    <div
                        v-permission="'organization_edit'"
                        class="card-tools pr-2 pointer">
                        <a  @click="editPeriod()">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>

                </div>

                <div class="card-body">

                    <p class="text-muted">
                        {{ trans('global.period.fields.begin') }}: {{ this.currentPeriod.begin }}<br>
                        {{ trans('global.period.fields.end') }}: {{ this.currentPeriod.end }}
                    </p>
                    <hr>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.currentPeriod.updated_at }}
                    </small>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <PeriodModal></PeriodModal>
        </Teleport>
    </div>
</template>

<script>
import PeriodModal from "../period/PeriodModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: "period",
    components:{
        PeriodModal
    },
    props: {
        period: {
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
            showPeriodModal: false,
            currentPeriod: {},
        }
    },
    mounted() {
        this.currentPeriod = this.period;
        this.$eventHub.on('period-updated', (period) => {
            this.globalStore?.closeModal('period-modal');
            this.currentPeriod = period;
        });

    },
    methods: {
        editPeriod(){
            this.globalStore?.showModal('period-modal', this.currentPeriod);
        },
    }
}
</script>
