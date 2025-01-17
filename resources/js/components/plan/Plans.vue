<template >
    <div class="row">
        <div class="col-md-12">
            <ul v-if="typeof (this.subscribable_type) == 'undefined'
                    && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills py-2"
                role="tablist"
            >
                <li class="nav-item">
                    <a
                        id="curriculum-filter-all"
                        class="nav-link"
                        :class="filter === 'all' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('all')"
                    >
                        <i class="fas fa-clipboard-list pr-2"></i>
                        {{ trans('global.all') }} {{ trans('global.plan.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        id="custom-filter-by-organization"
                        class="nav-link"
                        :class="filter === 'by_organization' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('by_organization')"
                    >
                        <i class="fas fa-university pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                    </a>
                </li>
                <li
                    v-can="'curriculum_create'"
                    class="nav-item"
                >
                    <a
                        id="custom-filter-owner"
                        class="nav-link"
                        :class="filter === 'owner' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('owner')"
                    >
                        <i class="fa fa-user pr-2"></i>
                        {{ trans('global.my') }} {{ trans('global.plan.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        id="custom-filter-shared-with-me"
                        class="nav-link"
                        :class="filter === 'shared_with_me' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_with_me')"
                    >
                        <i class="fa fa-paper-plane pr-2"></i>
                        {{ trans('global.shared_with_me') }}
                    </a>
                </li>
                <li
                    v-can="'curriculum_create'"
                    class="nav-item"
                >
                    <a
                        id="custom-tabs-shared-by-me"
                        class="nav-link"
                        :class="filter === 'shared_by_me' ? 'active' : ''"
                        data-toggle="pill"
                        role="tab"
                        @click="setFilter('shared_by_me')"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>{{ trans('global.shared_by_me') }}
                    </a>
                </li>
            </ul>
        </div>

        <div
            id="plan-content"
            class="col-md-12 m-0"
        >
            <IndexWidget
                v-permission="'plan_create'"
                key="planCreate"
                modelName="Plan"
                url="/plans"
                :create=true
                :label="trans('global.plan.' + create_label_field)"
            >
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                        class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget v-for="plan in plans"
                :key="'planIndex'+plan.id"
                :model="plan"
                modelName= "plan"
                url="/plans"
            >
                <template v-slot:itemIcon>
                    <i class="fa fa-2x fa-clipboard-list"></i>
                </template>

                <template v-slot:dropdown
                    v-permission="'plan_edit, plan_delete'"
                >
                    <div
                        class="dropdown-menu dropdown-menu-right"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'plan_edit'"
                            :name="'edit-plan-' + plan.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editPlan(plan)"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.plan.edit') }}
                        </button>
                        <button v-if="plan.allow_copy"
                            :name="'copy-plan-'+plan.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="confirmCopy(plan)"
                        >
                            <i class="fa fa-copy mr-2"></i>
                            {{ trans('global.plan.copy') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-permission="'plan_delete'"
                            :id="'delete-plan-' + plan.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmDelete(plan)"
                        >
                            <span v-if="create_label_field == 'enrol'">
                                <i class="fa fa-unlink mr-2"></i>
                                {{ trans('global.plan.expel') }}
                            </span>
                            <span v-else>
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.plan.delete') }}
                            </span>
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>
        <div
            id="plan-datatable-wrapper"
            class="w-100 dataTablesWrapper"
        >
            <DataTable
                id="plan-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none;"
            ></DataTable>
        </div>

        <Teleport to="body">
            <SubscribePlanModal/>
            <PlanModal/>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.plan.' + delete_label_field)"
                :description="trans('global.plan.' + delete_label_field +'_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
            <ConfirmModal
                :showConfirm="this.showCopy"
                :title="trans('global.plan.copy')"
                :description="trans('global.plan.copy_helper')"
                css='primary'
                @close="() => {
                    this.showCopy = false;
                }"
                @confirm="() => {
                    this.showCopy = false;
                    this.copy();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>

<script>
import SubscribePlanModal from "../plan/SubscribePlanModal.vue";
import PlanModal from "../plan/PlanModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {
        create_label_field: {
            type: String,
            default: 'create'
        },
        delete_label_field: {
            type: String,
            default: 'delete'
        },
        subscribable_type: '',
        subscribable_id: '',
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            plans: null,
            search: '',
            showConfirm: false,
            showCopy: false,
            url: '/plans/list',
            errors: {},
            currentPlan: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('plan-added', (plan) => {
            this.plans.push(plan);
        });

        this.$eventHub.on('plan-updated', (plan) => {
            this.update(plan);
        });

        this.$eventHub.on('plan-subscription-added', () => {
            this.loaderEvent();
        });

        this.$eventHub.on('filter', (filter) => {
                this.dt.search(filter).draw();
        });
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined') {
                this.url = '/planSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id;
            } else {
                this.url = '/plans/list?filter=' + this.filter;
            }

            this.dt.ajax.url(this.url).load();
        },
        editPlan(plan) {
            this.globalStore?.showModal('plan-modal', plan);
        },
        loaderEvent() {
            this.dt = $('#plan-datatable').DataTable();

            this.dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.plans = this.dt.rows({page: 'current'}).data().toArray();

                $('#plan-content').insertBefore('#plan-datatable-wrapper');
            });
        },
        confirmDelete(plan) {
            this.currentPlan = plan;
            this.showConfirm = true;
        },
        confirmCopy(plan) {
            this.currentPlan = plan;
            this.showCopy = true;
        },
        copy() {
            window.location = "/plans/" + this.currentPlan.id + "/copy";
        },
        destroy() {
            axios.delete('/plans/' + this.currentPlan.id)
                .then(res => {
                    let index = this.plans.indexOf(this.currentPlan);
                    this.plans.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        update(updatedPlan) {
            let plan = this.plans.find(plan => plan.id === updatedPlan.id);
            Object.assign(plan, updatedPlan);
        }
    },
    components: {
        SubscribePlanModal,
        ConfirmModal,
        DataTable,
        PlanModal,
        IndexWidget,
    },
}
</script>
