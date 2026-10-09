<template>
    <div class="d-flex flex-column">
        <div
            id="period-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'period_create'"
                key="'periodCreate'"
                modelName="Period"
                url="/periods"
                :create=true
                :label="trans('global.period.create')"
            />
            <IndexWidget v-for="period in periods"
                :key="'periodIndex' + period.id"
                :model="period"
                modelName="Period"
                url="/periods"
            >
                <template #icon>
                    <i class="fa fa-history"></i>
                </template>

                <template #dropdown
                    v-permission="'period_edit, period_delete'"
                >
                    <div class="dropdown-menu dropdown-menu-end">
                        <button
                            v-permission="'period_edit'"
                            type="button"
                            class="dropdown-item"
                            @click="editPeriod(period)"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.period.edit') }}
                        </button>

                        <hr class="my-1">

                        <button
                            v-permission="'period_delete'"
                            type="submit"
                            class="dropdown-item text-danger"
                            @click="confirmItemDelete(period)"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.period.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="period-datatable"
            :columns="columns"
            :options="$dtOptions"
            ajax="/periods/list"
            class="d-none"
            @xhr="(e, settings, json) => periods = json.data"
        />

        <Teleport to="body">
            <PeriodModal/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.period.delete')"
                :description="trans('global.period.delete_helper')"
                @close="showConfirm = false"
                @confirm="destroy()"
            />
        </Teleport>
    </div>
</template>
<script>
import PeriodModal from "../period/PeriodModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        ConfirmModal,
        DataTable,
        PeriodModal,
        IndexWidget,
    },
    data() {
        return {
            component_id: this.$.uid,
            periods: null,
            showConfirm: false,
            currentPeriod: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('period-added', period => {
            this.periods.push(period);
        });

        this.$eventHub.on('period-updated', updatedPeriod => {
            let period = this.periods.find(p => p.id === updatedPeriod.id);

            Object.assign(period, updatedPeriod);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        editPeriod(period) {
            this.globalStore.showModal('period-modal', period);
        },
        confirmItemDelete(period) {
            this.currentPeriod = period;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/periods/' + this.currentPeriod.id)
                .then(res => {
                    this.showConfirm = false;
                    let index = this.periods.indexOf(this.currentPeriod);
                    this.periods.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.showConfirm = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
    },
}
</script>