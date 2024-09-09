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
                        class="card-tools pr-2">
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
            <PeriodModal
                :show="this.showPeriodModal"
                @close="this.showPeriodModal = false"
                :params="this.currentPeriod"
            ></PeriodModal>
        </Teleport>
    </div>
</template>

<script>
import PeriodModal from "../period/PeriodModal";

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
            this.currentPeriod = period;
            this.showPeriodModal = false;
        });

    },
    methods: {
        editPeriod(){
            this.showPeriodModal = true;
        },
    }
}
</script>
