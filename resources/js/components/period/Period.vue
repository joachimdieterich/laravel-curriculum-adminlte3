<template>
    <div class="px-3">
        <div class="col-lg-4 col-12 mb-3 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-history mx-1"></i>{{ period.title }}
                </h5>

                <button v-if="checkPermission('period_edit')"
                    type="button"
                    class="btn btn-icon-alt"
                    @click="editPeriod()"
                >
                    <i class="fa fa-pencil-alt"></i>
                </button>
            </div>

            <div class="px-3 py-2 bg-white">
                <span class="text-muted">
                    {{ trans('global.period.fields.begin') }}: {{ period.begin }}<br>
                    {{ trans('global.period.fields.end') }}: {{ period.end }}
                </span>
            </div>

            <div class="p-2 bg-white rounded-bottom-3">
                <small>{{ period.updated_at }}</small>
            </div>
        </div>

        <Teleport to="body">
            <PeriodModal/>
        </Teleport>
    </div>
</template>
<script>
import PeriodModal from "../period/PeriodModal.vue";

export default {
    name: "Period",
    components: { PeriodModal },
    props: {
        period: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            showPeriodModal: false,
        }
    },
    mounted() {
        this.$eventHub.on('period-updated', () => window.location.reload());
    },
    methods: {
        editPeriod() {
            this.globalStore.showModal('period-modal', this.period);
        },
    },
}
</script>