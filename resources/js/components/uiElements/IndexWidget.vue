<template>
    <div  :id="item.DT_RowId"
         v-bind:value="item.DT_RowId"
         class="box box-objective nav-item-box-image pointer my-1 "
          :class="active === false ? 'not-allowed' : ''"
         style="min-width: 200px !important;"
         :style="'border-bottom: 5px solid ' + item.color"
    >
        <a v-if="this.create === true"
           @click="createNewItem()"
        >
            <div class="d-flex align-items-center justify-content-center nav-item-box-image-size">
                <slot name="itemIcon">
                    <i class="fa fa-2x p-5 fa-plus nav-item-text text-muted"></i>
                </slot>
            </div>
            <span class="text-center p-1 overflow-auto nav-item-box bg-gray-light">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ createLabel }}
                   </h1>
            </span>
        </a>
        <a v-else
           class="text-decoration-none text-black"
           :style="isSelected(item) ? 'filter: brightness(80%); width:100%;  height:100%;  position:absolute;  top:0;left:0;' :  ''"
        >
            <div v-if="item.medium_id"
                 @click="clickEvent(item)"
                 class="nav-item-box-image-size"
                 :style="{'background': 'url(/media/' + item.medium_id + '?model='+modelName+'&model_id=' + item.DT_RowId +') top center no-repeat'}">
                <div class="nav-item-box-image-size"
                     style="width: 100% !important;"
                     :style="{backgroundColor: item.color + ' !important',  'opacity': '0.25'}">
                </div>
            </div>
            <div v-else
                 @click="clickEvent(item)"
                 class="d-flex align-items-center justify-content-center nav-item-box-image-size"
                 :style="{backgroundColor: item.color + ' !important'}">
                <slot name="itemIcon"/>
            </div>
            <span @click="clickEvent(item)">
                <slot name="content">
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                       <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                           {{ item[this.titleField] }}
                       </h1>
                       <p class="text-muted small">
                           {{ htmlToText(item[this.descriptionField])}}
                       </p>
                    </span>
                </slot>
            </span>

            <slot name="owner"></slot>

            <div @click="clickEvent(item)"
                 class="symbol"
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
import { storeToRefs } from 'pinia';
import { useDatatableStore } from "../../store/datatables";
import { useToast } from "vue-toastification";

export default {
    props: {
        model: {},
        modelName: String,
        url: String,
        titleField: {
            type: String,
            default: 'title'
        },
        descriptionField: {
            type: String,
            default: 'description'
        },
        urlOnly: false,
        urlTarget: '_self',
        create: false,
        createLabel: String,
        storeTitle: String, //data store
        color: {
            type: String,
            default: '#27AE60'
        },
        active: {
            type: Boolean,
            default: true
        },
        info_deactivated:  {
            type: String,
            default: 'Zugriff nicht möglich'
        },
    },
    setup () { //use database store
        const store = useDatatableStore();
        const { getDatatable } = storeToRefs(store);
        const { isSelected } = storeToRefs(store);
        const toast = useToast();
        return {
            store,
            toast
        }
    },
    data() {
        return {
            name: "IndexWidget",
            item: {},
            href: "",
        }
    },

    mounted() {
        if (!this.create) {
            this.item = this.model;
            this.item.description = this.model.description ?? ''; //fallback
            this.item.color = this.item.color ?? this.color; //fallback
            this.item.medium_id = this.item.medium_id ?? null; //fallback
        }
    },

    methods: {
        isSelected(item){
            return (this.store.isSelected(this.storeTitle, item));
        },
        createNewItem(){
            this.$eventHub.emit('create'+this.modelName, true);
        },
        clickEvent(item){
            if (this.active){
                if (this.store.getDatatable(this.storeTitle)?.select){ // selectMode
                    this.store.addSelectItems(this.storeTitle, item);
                } else {
                    if (this.urlOnly){
                        window.open( this.url /*+ '/' + item.id*/, this.urlTarget);
                    } else {
                        window.location = this.url + '/' + item.id; //item.DT_RowId ? -> will not work for new entries
                    }
                }
            } else {
                this.infoNotification(this.info_deactivated);
            }
        },
        infoNotification(message) {
            this.toast.info(message, {
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false
            });
        },
    }
}
</script>
<style>

.not-allowed {
    cursor: not-allowed;
    filter: opacity(50%);
    width:100%;
    height:100%;
    position:absolute;
    top:0;
    left:0;
}
</style>
