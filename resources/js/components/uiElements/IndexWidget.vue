<template>
    <div  :id="item.DT_RowId"
         v-bind:value="item.DT_RowId"
         class="box box-objective nav-item-box-image pointer my-1 "
         style="min-width: 200px !important;"
         :style="'border-bottom: 5px solid ' + item.color"
    >
        <a v-if="this.create === true"
           @click="createNewItem()"
        >
            <div class="nav-item-box-image-size text-center">
                <i class="fa fa-2x p-5 fa-plus nav-item-text text-muted"></i>
            </div>
            <span class="text-center p-1 overflow-auto nav-item-box bg-gray-light">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ createLabel }}
                   </h1>
            </span>
        </a>
        <a v-else
            :href="url + '/' + item.DT_RowId"
           class="text-decoration-none text-black"
        >
            <div v-if="item.medium_id"
                 class="nav-item-box-image-size"
                 :style="{'background': 'url(/media/' + item.medium_id + '?model='+modelName+'&model_id=' + item.DT_RowId +') top center no-repeat', 'background-size': 'cover', }">
                <div class="nav-item-box-image-size"
                     style="width: 100% !important;"
                     :style="{backgroundColor: item.color + ' !important',  'opacity': '0.5'}">
                </div>
            </div>
            <div v-else
                 class="nav-item-box-image-size text-center"
                 :style="{backgroundColor: item.color + ' !important'}">
            </div>
            <span class="bg-white text-center p-1 overflow-auto nav-item-box">
               <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                   {{ item.title }}
               </h1>
               <p class="text-muted small"
                  v-html="htmlToText(item.description)">
               </p>
            </span>

            <slot name="owner"></slot>

            <div class="symbol"
                 :style="'color:' + $textcolor(item.color) + '!important'"
                 style="position: absolute; width: 30px; height: 40px;">
                <slot name="icon"></slot>
            </div>
            <div v-permission="'is_admin'"
                 class="btn btn-flat pull-right"
                 :id="model+'Dropdown_' + item.DT_RowId"
                 style="position:absolute; top:0; right: 0; background-color: transparent;"
                 data-toggle="dropdown"
                 aria-expanded="false"
            >
                <i class="fas fa-ellipsis-v"
                   :style="'color:' + $textcolor(item.color)"></i>
                <slot name="dropdown"></slot>
            </div>
        </a>
    </div>
</template>

<script>
export default {
    props: {
        model: {},
        modelName: String,
        url: String,
        create: false,
        createLabel: String,
    },
    data() {
        return {
            name: "IndexWidget",
            item: {},
        }
    },

    mounted() {
        if (!this.create) {
            this.item = this.model;
            this.item.color = this.item.color ?? '#27AE60'; //fallback
            this.item.medium_id = this.item.medium_id ?? null; //fallback
        }
    },
    methods: {
        createNewItem(){
            this.$eventHub.emit('create'+this.modelName, true);
        }
    }
}
</script>

<style scoped>

</style>
