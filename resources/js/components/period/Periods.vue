<template >
    <div class="row">
        <div id="period-content"
             class="col-md-12 m-0">
            <IndexWidget
                v-permission="'period_create'"
                key="'periodCreate'"
                modelName="Period"
                url="/periods"
                :create=true
                :label="trans('global.period.create')">
            </IndexWidget>
            <IndexWidget
                v-for="period in periods"
                :key="'periodIndex'+period.id"
                :model="period"
                modelName= "period"
                url="/periods">
                <template v-slot:icon>
                    <i class="fa fa-history pt-2"></i>
                </template>

                <template
                    v-permission="'period_edit, period_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'period_edit'"
                            :name="'edit-period-' + period.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editPeriod(period)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.period.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'period_delete'"
                            :id="'delete-period-' + period.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(period)">
                            <i class="fa fa-trash mr-2"></i>
                            {{ trans('global.period.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div id="period-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="period-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <PeriodModal></PeriodModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.period.delete')"
                :description="trans('global.period.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>


<script>
import PeriodModal from "../period/PeriodModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global.js";
DataTable.use(DataTablesCore);

export default {
    props: {},
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            periods: null,
            search: '',
            showPeriodModal: false,
            showConfirm: false,
            url: '/periods/list',
            errors: {},
            currentPeriod: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'begin', data: 'begin'},
                { title: 'end', data: 'end', searchable: true},
            ],
            options : this.$dtOptions,
            modalMode: 'edit'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('period-added', (period) => {
            this.globalStore?.closeModal('period-modal');
            this.periods.push(period);
        });

        this.$eventHub.on('period-updated', (period) => {
            this.globalStore?.closeModal('period-modal');
            this.update(period);
        });
        this.$eventHub.on('createPeriod', () => {
            this.globalStore?.showModal('period-modal', {});
        });
    },
    methods: {
        editPeriod(period){
            this.globalStore?.showModal('period-modal', period);
        },
        loaderEvent(){
            const dt = $('#period-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.periods = dt.rows({page: 'current'}).data().toArray();

                $('#period-content').insertBefore('#period-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        confirmItemDelete(period){
            this.currentPeriod = period;
            this.showConfirm = true;
        },
        destroy() {
            axios.delete('/periods/' + this.currentPeriod.id)
                .then(res => {
                    let index = this.periods.indexOf(this.currentPeriod);
                    this.periods.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(period) {
            const index = this.periods.findIndex(
                vc => vc.id === period.id
            );

            for (const [key, value] of Object.entries(period)) {
                this.periods[index][key] = value;
            }
        }
    },
    components: {
        ConfirmModal,
        DataTable,
        PeriodModal,
        IndexWidget
    },
}
</script>
