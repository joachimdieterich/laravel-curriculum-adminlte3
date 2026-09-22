<template >
    <div class="d-flex flex-column">
        <ul v-if="!subscribable"
            class="nav nav-pills px-3 py-2"
            role="tablist"
        >
            <li class="nav-item pointer">
                <a
                    id="curriculum-filter-all"
                    class="nav-link"
                    :class="filter === 'all' ? 'active' : ''"
                    data-toggle="pill"
                    role="tab"
                    @click="setFilter('all')"
                >
                    <i class="fas fa-clipboard-list pe-2"></i>
                    {{ trans('global.all') }} {{ trans('global.plan.title') }}
                </a>
            </li>
            <li class="nav-item pointer">
                <a
                    id="custom-filter-by-organization"
                    class="nav-link"
                    :class="filter === 'by_organization' ? 'active' : ''"
                    data-toggle="pill"
                    role="tab"
                    @click="setFilter('by_organization')"
                >
                    <i class="fas fa-university pe-2"></i>
                    {{ trans('global.my') }} {{ trans('global.organization.title_singular') }}
                </a>
            </li>
            <li
                v-permission="'plan_create'"
                class="nav-item pointer"
            >
                <a
                    id="custom-filter-owner"
                    class="nav-link"
                    :class="filter === 'owner' ? 'active' : ''"
                    data-toggle="pill"
                    role="tab"
                    @click="setFilter('owner')"
                >
                    <i class="fa fa-user pe-2"></i>
                    {{ trans('global.my') }} {{ trans('global.plan.title') }}
                </a>
            </li>
            <li class="nav-item pointer">
                <a
                    id="custom-filter-shared-with-me"
                    class="nav-link"
                    :class="filter === 'shared_with_me' ? 'active' : ''"
                    data-toggle="pill"
                    role="tab"
                    @click="setFilter('shared_with_me')"
                >
                    <i class="fa fa-paper-plane pe-2"></i>
                    {{ trans('global.shared_with_me') }}
                </a>
            </li>
            <li
                v-permission="'plan_create'"
                class="nav-item pointer"
            >
                <a
                    id="custom-tabs-shared-by-me"
                    class="nav-link"
                    :class="filter === 'shared_by_me' ? 'active' : ''"
                    data-toggle="pill"
                    role="tab"
                    @click="setFilter('shared_by_me')"
                >
                    <i class="fa fa-share-nodes  pe-2"></i>{{ trans('global.shared_by_me') }}
                </a>
            </li>
        </ul>

        <div
            id="plan-content"
            class="px-3"
        >
            <IndexWidget
                v-permission="'plan_create'"
                key="planCreate"
                modelName="Plan"
                url="/plans"
                :create=!subscribable
                :subscribe="subscribable"
                :subscribable_id="subscribable_id"
                :subscribable_type="subscribable_type"
                :label="trans('global.plan.' + createLabel)"
            >
                <template #itemIcon>
                    <i v-if="subscribable"
                        class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget v-for="plan in plans"
                :key="'planIndex' + plan.id"
                :model="plan"
                modelName="Plan"
                url="/plans"
                :showSubscribable="subscribable"
            >
                <template #itemIcon>
                    <i class="fa fa-2x fa-clipboard-list"></i>
                </template>

                <template #dropdown>
                    <div v-if="subscribable"
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button
                            v-permission="'plan_delete'"
                            :id="'delete-plan-' + plan.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmDelete(plan)"
                        >
                            <i class="fa fa-unlink me-2"></i>
                            {{ trans('global.plan.expel') }}
                        </button>
                    </div>
                    <div v-else
                        class="dropdown-menu dropdown-menu-end"
                        style="z-index: 1050;"
                        x-placement="left-start"
                    >
                        <button v-if="plan.owner_id == $userId || checkPermission('is_admin')"
                            v-permission="'plan_edit'"
                            :name="'edit-plan-' + plan.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editPlan(plan)"
                        >
                            <i class="fa fa-pencil-alt me-2"></i>
                            {{ trans('global.plan.edit') }}
                        </button>

                        <button v-if="plan.owner_id == $userId || checkPermission('is_admin')"
                            :name="'plan-share_' + plan.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="sharePlan(plan)"
                        >
                            <i class="fa fa-share-alt me-2"></i>
                            {{ trans('global.plan.share') }}
                        </button>

                        <button v-if="plan.allow_copy"
                            :name="'copy-plan-' + plan.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="confirmCopy(plan)"
                        >
                            <i class="fa fa-copy me-2"></i>
                            {{ trans('global.plan.copy') }}
                        </button>

                        <hr v-if="plan.owner_id == $userId || checkPermission('is_admin')"
                            class="my-1"
                        />

                        <button v-if="plan.owner_id == $userId || checkPermission('is_admin')"
                            v-permission="'plan_delete'"
                            :id="'delete-plan-' + plan.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmDelete(plan)"
                        >
                            <i class="fa fa-trash me-2"></i>
                            {{ trans('global.plan.delete') }}
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <DataTable
            ref="datatable"
            id="plan-datatable"
            :columns="columns"
            :options="options"
            :ajax="subscribable ? '/plans/list?group_id=' + subscribable_id : '/plans/list'"
            class="d-none"
            @xhr="(e, settings, json) => plans = json.data"
        />

        <Teleport to="body">
            <PlanModal v-if="!subscribable"/>
            <MediumModal v-if="!subscribable"/>
            <SubscribeModal v-if="!subscribable"/>
            <SubscribePlanModal v-if="subscribable"/>
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.plan.' + deleteLabel)"
                :description="trans('global.plan.' + deleteLabel +'_helper')"
                @close="showConfirm = false"
                @confirm="() => {
                    showConfirm = false;
                    destroy();
                }"
            />
            <ConfirmModal v-if="!subscribable"
                :showConfirm="showCopy"
                :title="trans('global.plan.copy')"
                :description="trans('global.plan.copy_helper')"
                css='primary'
                @close="showCopy = false"
                @confirm="() => {
                    showCopy = false;
                    copy();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import PlanModal from "../plan/PlanModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import SubscribePlanModal from "../plan/SubscribePlanModal.vue";
import MediumModal from "../media/MediumModal.vue";
import IndexWidget from "../uiElements/IndexWidget.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
DataTable.use(DataTablesCore);

export default {
    components: {
        PlanModal,
        SubscribeModal,
        SubscribePlanModal,
        MediumModal,
        ConfirmModal,
        DataTable,
        IndexWidget,
    },
    props: {
        subscribable: {
            type: Boolean,
            default: false,
        },
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            plans: null,
            showConfirm: false,
            showCopy: false,
            currentPlan: {},
            columns: [
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true },
            ],
            options : this.$dtOptions,
            filter: 'all',
            dt: null,
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

        this.dt = this.$refs.datatable.dt;

        this.$eventHub.on('plan-subscription-added', planSubscription => {
            this.plans.push(planSubscription.plan);
        });

        this.$eventHub.on('plan-added', plan => {
            this.plans.push(plan);
        });

        this.$eventHub.on('plan-updated', plan => {
            this.update(plan);
        });

        this.$eventHub.on('filter', filter => {
            this.dt.search(filter.searchString).draw();
        });
    },
    methods: {
        setFilter(filter) {
            this.filter = filter;
            this.dt.ajax.url('/plans/list?filter=' + this.filter).load();
        },
        editPlan(plan) {
            this.globalStore.showModal('plan-modal', plan);
        },
        sharePlan(plan) {
            this.globalStore.showModal('subscribe-modal', {
                modelId: plan.id,
                modelUrl: 'plan' ,
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: false,
                canEditCheckbox: true,
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
            axios.get('/plans/' + this.currentPlan.id + '/copy')
                .then(response => {
                    this.plans.push(response.data);
                });
        },
        destroy() {
            if (this.subscribable) {
                axios.post('/planSubscriptions/expel', {
                    model_id : this.currentPlan.id,
                    subscribable_type : this.subscribable_type,
                    subscribable_id : this.subscribable_id,
                })
                    .then(response => {
                        let index = this.plans.indexOf(this.currentPlan);
                        this.plans.splice(index, 1);
                        this.toast.success(response.data);
                    })
                    .catch(e => {
                        this.toast.error(trans('global.expel_error'));
                    });
            } else {
                axios.delete('/plans/' + this.currentPlan.id)
                    .then(response => {
                        let index = this.plans.indexOf(this.currentPlan);
                        this.plans.splice(index, 1);
                    })
                    .catch(e => {
                        console.log(e);
                    });
            }
        },
        update(updatedPlan) {
            let plan = this.plans.find(plan => plan.id === updatedPlan.id);
            Object.assign(plan, updatedPlan);
        }
    },
    computed: {
        createLabel() {
            return this.subscribable ? 'enrol' : 'create';
        },
        deleteLabel() {
            return this.subscribable ? 'expel' : 'delete';
        },
    },
}
</script>