<template>
    <div
        :id="'plan-' + plan.id"
        class="box box-objective nav-item-box-image pointer my-1"
        style="min-width: 200px !important;"
        :style="'border-bottom: 5px solid ' + plan.color"
    >
        <a :href="'/plans/' + plan.id" class="text-decoration-none text-black">
            <div v-if="plan.medium_id" class="nav-item-box-image-size"
                :style="{ 'background': 'url(/media/' + plan.medium_id + '?model=plan&model_id=' + plan.id + ') top center no-repeat', 'background-size': 'cover', }">
                <div class="nav-item-box-image-size" style="width: 100% !important;"
                    :style="{ backgroundColor: plan.color + ' !important', 'opacity': '0.5' }">
                </div>
            </div>
            <div v-else class="nav-item-box-image-size text-center"
                :style="'background-color: ' + (plan.color ?? '#2980B9') + ' !important; color: ' + $textcolor(plan.color) + ' !important;'">
                <i class="fa fa-calendar-day   fa-2x p-5 nav-item-text"></i>
            </div>
            <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                    {{ plan.title }}
                </h1>
                <p class="text-muted small" v-dompurify-html="htmlToText(plan.description)"></p>
            </span>
            <div class="symbol"
                :style="'color:' + $textcolor(plan.color) + ' !important'"
                style="position: absolute; width: 30px; height: 40px;"
            >
                <i v-if="$userId == plan.owner_id" class="fa fa-user pt-2"></i>
                <i v-else class="fa fa-share-nodes pt-2"></i>
            </div>
            <div v-if="$userId == plan.owner_id || plan.allow_copy"
                class="btn btn-flat pull-right "
                :id="'planDropdown_' + plan.id"
                style="position:absolute; top:0; right: 0; background-color: transparent; z-index: 1050;"
                data-toggle="dropdown"
                aria-expanded="false"
            >
                <i class="fas fa-ellipsis-v"
                    :style="'color:' + $textcolor(plan.color)"></i>
                <div class="dropdown-menu dropdown-menu-right"
                    x-placement="left-start"
                >
                    <button
                        v-if="$userId == plan.owner_id"
                        :name="'planEdit_'+plan.id"
                        class="dropdown-item text-secondary"
                        @click.prevent="$parent.editPlan(plan)"
                    >
                        <i class="fa fa-pencil-alt mr-2"></i>
                        {{ trans('global.plan.edit') }}
                    </button>
                    <button
                        v-if="plan.allow_copy"
                        :id="'copy-plan-'+plan.id"
                        type="submit"
                        class="dropdown-item text-secondary py-1"
                        @click.prevent="$parent.confirmPlanCopy(plan.id)"
                    >
                        <i class="fa fa-copy mr-2"></i>
                        {{ trans('global.plan.copy') }}
                    </button>
                    <hr v-if="$userId == plan.owner_id" class="my-1">
                    <button
                        v-if="$userId == plan.owner_id"
                        :id="'delete-plan-' + plan.id"
                        type="submit"
                        class="dropdown-item py-1 text-red"
                        @click.prevent="$parent.confirmItemDelete(plan.id)"
                    >
                        <i class="fa fa-trash mr-2"></i>
                        {{ trans('global.plan.delete') }}
                    </button>
                </div>
            </div>
        </a>
    </div>
</template>
<script>
export default {
    name: 'PlanIndexWidget',
    props: {
        plan: {},
        search: {
            type: String,
            default: ''
        }
    },
}
</script>
<style scoped>
@media only screen and (max-width: 991px) {
    .fa-ellipsis-v { color: black !important; }
}
</style>
