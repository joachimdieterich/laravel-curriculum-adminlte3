<template>
    <div
        :id="'logbook-' + logbook.id"
        class="box box-objective nav-item-box-image pointer my-1"
        style="min-width: 200px !important;"
        :style="'border-bottom: 5px solid ' + logbook.color"
    >
        <a :href="'/logbooks/' + logbook.id" class="text-decoration-none text-black">
            <div v-if="logbook.medium_id" class="nav-item-box-image-size"
                :style="{ 'background': 'url(/media/' + logbook.medium_id + '?model=logbook&model_id=' + logbook.id + ') top center no-repeat', 'background-size': 'cover', }">
                <div class="nav-item-box-image-size" style="width: 100% !important;"
                    :style="{ backgroundColor: logbook.color + ' !important', 'opacity': '0.5' }">
                </div>
            </div>
            <div v-else class="nav-item-box-image-size text-center"
                :style="'background-color: ' + (logbook.color ?? '#2980B9') + ' !important; color: ' + $textcolor(logbook.color) + ' !important;'">
                <i :class="logbook.css_icon + ' fa-2x p-5 nav-item-text'"></i>
            </div>
            <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                    {{ logbook.title }}
                </h1>
                <p class="text-muted small" v-html="htmlToText(logbook.description)">
                </p>
            </span>
            <div class="symbol"
                :style="'color:' + $textcolor(logbook.color) + '!important'"
                style="position: absolute; width: 30px; height: 40px;"
            >
                <i v-if="$userId == logbook.owner_id"
                    class="fa fa-user pt-2"></i>
                <i v-else
                    class="fa fa-share-nodes pt-2"></i>
            </div>
            <div v-if="$userId == logbook.owner_id"
                class="btn btn-flat pull-right "
                :id="'logbookDropdown_' + logbook.id"
                style="position:absolute; top:0; right: 0; background-color: transparent;"
                data-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-ellipsis-v"
                    :style="'color:' + $textcolor(logbook.color)"></i>
                <div class="dropdown-menu dropdown-menu-right"
                    x-placement="left-start">
                    <button :name="'logbookEdit_'+logbook.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="$parent.editLogbook(logbook)">
                        <i class="fa fa-pencil-alt mr-2"></i>
                        {{ trans('global.logbook.edit') }}
                    </button>
                    <hr class="my-1">
                    <button :id="'delete-logbook-' + logbook.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="$parent.confirmItemDelete(logbook.id)">
                        <i class="fa fa-trash mr-2"></i>
                        {{ trans('global.logbook.delete') }}
                    </button>
                </div>
            </div>
        </a>
    </div>
</template>
<script>
export default {
    name: 'LogbookIndexWidget',
    props: {
        logbook: {},
        search: {
            type: String,
            default: ''
        }
    },
    methods: {},
}
</script>
<style scoped>
@media only screen and (max-width: 991px) {
  .fa-ellipsis-v { color: black !important; }
}
</style>
