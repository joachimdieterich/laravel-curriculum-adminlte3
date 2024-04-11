<template >
    <div>
        <ul
            v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
            class="nav nav-pills"
            role="tablist"
        >
            <li class="nav-item">
                <a class="nav-link "
                    :class="filter === 'all' ? 'active' : ''"
                    id="plan-filter-all"
                    @click="setFilter('all')"
                    data-toggle="pill"
                    role="tab"
                >
                    <i class="fa fa-list-check pr-2"></i>Alle Pläne
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    :class="filter === 'owner' ? 'active' : ''"
                    id="custom-filter-owner"
                    @click="setFilter('owner')"
                    data-toggle="pill"
                    role="tab"
                >
                    <i class="fa fa-user  pr-2"></i>Meine Pläne
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    :class="filter === 'shared_with_me' ? 'active' : ''"
                    id="custom-filter-shared-with-me"
                    @click="setFilter('shared_with_me')"
                    data-toggle="pill"
                    role="tab"
                >
                    <i class="fa fa-paper-plane pr-2"></i>Für mich freigegeben
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    :class="filter === 'shared_by_me' ? 'active' : ''"
                    id="custom-tabs-shared-by-me"
                    @click="setFilter('shared_by_me')"
                    data-toggle="pill"
                    role="tab"
                >
                    <i class="fa fa-share-nodes  pr-2"></i>Von mir freigegeben
                </a>
            </li>
        </ul>

        <table id="plan-datatable" style="display: none;"></table>
        <div id="plan-content">
            <div class="py-2">
                <PlanIndexWidget
                    v-for="(plan,index) in plans"
                    :key="index+'_plan_'+plan.id"
                    :plan="plan"
                />
                <!-- <PlanIndexAddWidget
                    v-if="((this.filter == 'all' && typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined') || this.filter == 'owner')"
                    v-can="'plan_create'"
                /> -->

                <Modal
                    :id="'planModal'"
                    css="danger"
                    :title="trans('global.plan.delete')"
                    :text="trans('global.plan.delete_helper')"
                    :ok_label="trans('global.plan.delete')"
                    v-on:ok="destroy()"
                />
                <Modal
                    :id="'planCopyModal'"
                    css="primary"
                    :title="trans('global.plan.copy')"
                    :text="trans('global.plan.copy_helper')"
                    ok_label="OK"
                    v-on:ok="copy()"
                />
            </div>
        </div>
    </div>
</template>

<script>
import PlanIndexWidget from "./PlanIndexWidget";
// import PlanIndexAddWidget from "./PlanIndexAddWidget";
const Modal = () => import('./../uiElements/Modal');

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            plans: [],
            tempId: Number,
            errors: {},
            filter: 'all',
        }
    },
    methods: {
        loaderEvent() {
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/planSubscriptions?subscribable_type=' + this.subscribable_type + '&subscribable_id=' + this.subscribable_id;
            } else {
                this.url = '/plans/list?filter=' + this.filter;
            }

            $('#plan-datatable').DataTable().ajax.url(this.url).load();
        },
        editPlan(plan) {
            this.$eventHub.$emit('edit_plan', plan);
        },
        destroy() {
            axios.delete('/plans/' + this.tempId)
                .then(() => {
                    this.loaderEvent();
                })
                .catch ((e) => {
                    console.log(e);
            });
        },
        confirmItemDelete(id) {
            $('#planModal').modal('show');
            this.tempId = id;
        },
        confirmPlanCopy(id) {
            $('#planCopyModal').modal('show');
            this.tempId = id;
        },
        copy() {
            window.location = "/plans/" + this.tempId + "/copy";
        },
        setFilter(filter) {
            this.filter = filter;
            this.loaderEvent();
        },
    },
    mounted() {
        this.$eventHub.$emit('showSearchbar');

        const parent = this;
        let dt = $('#plan-datatable').DataTable({
            dom: 'tilpr',
            columns: [ // only gets attributes used in this component
                { title: 'id', data: 'id', searchable: false },
                { title: 'title', data: 'title', searchable: true },
                { title: 'description', data: 'description', searchable: true },
                { title: 'color', data: 'color', searchable: false },
                { title: 'owner_id', data: 'owner_id', searchable: false },
                // { title: 'medium_id', data: 'medium_id', searchable: false },
                { title: 'allow_copy', data: 'allow_copy', searchable: false },
            ],
        }).on('draw.dt', () => {  // checks if the datatable-data changes, to update the plan-data
            parent.plans = $('#plan-datatable').DataTable().rows({ page: 'current' }).data().toArray();
            $('#plan-content').insertBefore('#plan-datatable');
        });

        this.loaderEvent();

        this.$eventHub.$on('filter', (filter) => {
            dt.search(filter).draw();
        });
        this.$eventHub.$on('plan-updated', (plan) => {
            const index = this.plans.findIndex(
                p => p.id === plan.id
            );

            for (const [key, value] of Object.entries(plan)) {
                this.plans[index][key] = value;
            }
        });
    },
    components: {
        PlanIndexWidget,
        // PlanIndexAddWidget,
        Modal,
    },
}
</script>
<style>
#plan-datatable_wrapper { width: 100%; }
</style>
<style scoped>
.nav-link:hover {
    cursor: default;
    user-select: none;
}
.nav-item:hover .nav-link:not(.active) {
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer;
}
</style>