<template>
    <div>
        <div
            v-if="visible"
            :id="'plan-add'"
            class="box box-objective nav-item-box-image pointer my-1"
            style="min-width: 200px !important; border-bottom: 5px solid #28a745"
            @click="open()"
        >
            <div class="nav-item-box-image-size text-center bg-success">
                <i class="fa fa-2x p-5 fa-plus nav-item-text text-white"></i>
            </div>
    
            <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                    {{ trans('global.plan.create') }}
                </h1>
            </span>
        </div>
        <PlanCreate
            id="modal-plan-form"
            :method="method"
            :plan="plan"
        />
    </div>
</template>
<script>
import PlanCreate from "./PlanCreate";

export  default {
    name: 'PlanIndexAddWidget',
    props: {
        visible: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            plan: null,
            method: {
                type: String,
                default: 'post'
            }
        }
    },
    methods: {
       open(method = 'post'){
           this.method = method;
           $('#modal-plan-form').modal('show');
       },
    },
    mounted() {
        this.$eventHub.$on('edit_plan', (plan) => {
            this.plan = plan;
            this.open('patch');
        });
    },
    components: {
        PlanCreate
    },
}
</script>