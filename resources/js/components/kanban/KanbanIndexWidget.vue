<template>
    <div
        :id="'kanban-' + kanban.id"
        class="box box-objective nav-item-box-image pointer my-1"
        style="min-width: 200px !important;"
        :style="'border-bottom: 5px solid ' + kanban.color"
    >
        <a :href="'/kanbans/' + kanban.id"
            class="text-decoration-none text-black"
        >
            <div v-if="kanban.medium_id"
                class="nav-item-box-image-size"
            >
                <render-usage
                    class="d-flex align-items-center user-select-none"
                    :medium="{ id: kanban.medium_id }"
                    :style="{ backgroundColor: kanban.color + ' !important', minHeight: '150px', borderTopLeftRadius: '.2rem' }"
                    :enableClick="false"
                ></render-usage>
            </div>
            <div v-else
                class="nav-item-box-image-size text-center"
                :style="'background-color: ' + (kanban.color ?? '#2980B9') + ' !important; color: ' + $textcolor(kanban.color) + ' !important;'">
                <i class="fa fa-2x p-5 fa-columns nav-item-text"></i>
            </div>

            <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                    {{ kanban.title }}
                </h1>
                <p class="text-muted small">
                    {{ htmlToText(kanban.description) }}
                </p>
            </span>

            <div class="symbol"
                :style="'color:' + $textcolor(kanban.color) + ' !important'"
                style="position: absolute; width: 30px; height: 40px;"
            >
                <i v-if="$userId == kanban.owner_id" class="fa fa-user pt-2"></i>
                <i v-else class="fa fa-share-nodes pt-2"></i>
            </div>

            <div v-if="$userId == kanban.owner_id || kanban.allow_copy"
                class="btn btn-flat pull-right "
                :id="'kanbanDropdown_' + kanban.id"
                style="position:absolute; top:0; right: 0; background-color: transparent;"
                data-toggle="dropdown"
                aria-expanded="false"
            >
                <i class="fas fa-ellipsis-v" :style="'color:' + $textcolor(kanban.color)"></i>
                <div class="dropdown-menu dropdown-menu-right"
                    x-placement="left-start"
                >
                    <button
                        v-if="$userId == kanban.owner_id"
                        :name="'kanbanEdit_'+kanban.id"
                        class="dropdown-item text-secondary"
                        @click.prevent="$parent.editKanban(kanban)"
                    >
                        <i class="fa fa-pencil-alt mr-2"></i>
                        {{ trans('global.kanban.edit') }}
                    </button>
                    <button
                        v-if="kanban.allow_copy"
                        :id="'copy-kanban-'+kanban.id"
                        type="submit"
                        class="dropdown-item text-secondary py-1"
                        @click.prevent="$parent.confirmKanbanCopy(kanban.id)"
                    >
                        <i class="fa fa-copy mr-2"></i>
                        {{ trans('global.kanban.copy') }}
                    </button>
                    <hr v-if="$userId == kanban.owner_id" class="my-1">
                    <button
                        v-if="$userId == kanban.owner_id"
                        :id="'delete-kanban-'+kanban.id"
                        type="submit"
                        class="dropdown-item py-1 text-red"
                        @click.prevent="$parent.confirmItemDelete(kanban.id)"
                    >
                        <i class="fa fa-trash mr-2"></i>
                        {{ trans('global.kanban.delete') }}
                    </button>
                </div>
            </div>
        </a>
    </div>
</template>
<script>
const renderUsage = () => import('../../../../app/Plugins/Repositories/edusharing/resources/js/components/RenderUsage');

export default {
    name: 'KanbanIndexWidget',
    props: {
        kanban: {},
        search: {
            type: String,
            default: ''
        }
    },
    methods: {
        href: function (id) {
            return '/media/'+ id;
        },
    },
    components: {
        renderUsage,
    },
}
</script>
<style>
@media only screen and (max-width: 991px) {
    .fa-ellipsis-v { color: black !important; }
}
img.p-0.w-100 {
    opacity: 0.75;
}
</style>
