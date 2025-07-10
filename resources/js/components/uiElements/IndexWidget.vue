<template>
    <div 
        :id="item.DT_RowId"
        :value="item.DT_RowId"
        class="box box-objective nav-item-box-image pointer my-1 pull-left"
        :class="active === false ? 'not-allowed' : ''"
        style="min-width: 200px !important;"
        :style="{ 'border-color': item.color ?? '#F2F4F5', 'background-color': item.color + ' !important' }"
    >
        <a v-if="create || subscribe"
            @click="openModal()"
        >
            <div class="d-flex align-items-center justify-content-center">
                <slot name="itemIcon">
                    <i class="fa fa-2x fa-plus text-muted"></i>
                </slot>
            </div>
            <span>
                <span class="nav-item-box d-flex justify-content-center align-items-center align-items-lg-start bg-gray-light text-center p-1">
                    {{ label }}
                </span>
            </span>
        </a>
        <a v-else
            class="text-decoration-none"
            :style="'color: ' + $textcolor(item.color) + ' !important; ' + (isSelected() ? 'filter: brightness(80%); width:100%; height:100%; position: absolute; top: 0; left: 0;' : '')"
        >
            <div v-if="item.medium_id"
                @click="clickEvent(item)"
            >
                <div
                    class="nav-item-box-image-size h-100 w-100"
                    style="opacity: 75%"
                    :style="{'background': 'url(/media/' + item.medium_id + '?preview=true) center no-repeat'}"
                >
                </div>
            </div>
            <div v-else
                class="d-flex align-items-center justify-content-center"
                @click="clickEvent(item)"
            >
                <slot name="itemIcon"/>
            </div>
            <span @click="clickEvent(item)">
                <slot name="content">
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                        <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                            {{ item[titleField] }}
                        </h1>
                        <p class="text-muted small">
                            {{ htmlToText(item[descriptionField])}}
                        </p>
                        <slot name="badges"></slot>
                    </span>
                </slot>
            </span>

            <slot name="owner"></slot>

            <div
                class="symbol"
                @click="clickEvent(item)"
            >
                <slot name="icon">
                    <i
                        class="fa"
                        :class="item.owner_id == $userId ? 'fa-user' : 'fa-share-nodes'"
                    ></i>
                </slot>
            </div>

            <div v-if="(item.owner_id == $userId && !showSubscribable)
                    || (item.allow_copy && (modelName != 'Plan' || checkPermission('is_teacher')))
                    || (checkPermission('is_teacher') && (showSubscribable || modelName == 'Exam'))
                    || checkPermission('is_admin')
                "
                :id="model + 'Dropdown_' + item.DT_RowId"
                class="btn btn-flat position-absolute pull-right"
                style="top: 0; right: 0; background-color: transparent;"
                data-toggle="dropdown"
                aria-expanded="false"
            >
                <i class="fa fa-ellipsis-v"
                    :style="'color:' + (screenWidth > 990 ? $textcolor(item.color) : '#000')"
                ></i>
                <slot name="dropdown"></slot>
            </div>
        </a>
    </div>
</template>
<script>
import {storeToRefs} from 'pinia';
import {useGlobalStore} from "../../store/global";
import {useDatatableStore} from "../../store/datatables";
import {useToast} from "vue-toastification";

export default {
    props: {
        model: {
            type: Object,
            default: null,
        },
        modelName: {
            type: String,
            default: null,
        },
        url: {
            type: String,
            default: null,
        },
        titleField: {
            type: String,
            default: 'title',
        },
        descriptionField: {
            type: String,
            default: 'description',
        },
        urlOnly: {
            type: Boolean,
            default: false,
        },
        urlTarget: {
            type: String,
            default: '_self',
        },
        create: {
            type: Boolean,
            default: false,
        },
        subscribe: {
            type: Boolean,
            default: false,
        },
        showSubscribable: {
            type: Boolean,
            default: false,
        },
        subscribable_id: Number,
        subscribable_type: String,
        label: String,
        storeTitle: String, //data store
        color: {
            type: String,
            default: '#27AE60',
        },
        active: {
            type: Boolean,
            default: true,
        },
        info_deactivated: {
            type: String,
            default: 'Zugriff nicht möglich',
        },
    },
    setup() { //use database store
        const store = useDatatableStore();
        const globalStore = useGlobalStore();
        const { getDatatable } = storeToRefs(store);
        const { isSelected } = storeToRefs(store);
        const toast = useToast();
        return {
            store,
            globalStore,
            toast,
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
        if (!this.create && !this.subscribe) {
            this.item = this.model;
            this.item.description = this.model.description ?? ''; //fallback
            this.item.color = this.item.color ?? this.color; //fallback
            this.item.medium_id = this.item.medium_id ?? null; //fallback
        }
    },
    methods: {
        isSelected() {
            return (this.store.isSelected(this.storeTitle, this.model));
        },
        openModal() {
            let modal = this.subscribe
                ? 'subscribe-' + this.modelName.toLowerCase() + '-modal'
                : this.modelName.toLowerCase() + '-modal';

            this.globalStore?.showModal(modal, {
                // only gets used in subscribe-modals
                subscribable_id: this.subscribable_id,
                subscribable_type: this.subscribable_type,
            });
        },
        clickEvent(item) {
            if (this.active) {
                if (this.store.getDatatable(this.storeTitle)?.select) { // selectMode
                    // also select/deselect entry in datatable, in case the DataTable is visible
                    if (this.store.addSelectItems(this.storeTitle, this.model)) { // item got added
                        this.$parent.dt.row('#' + item.DT_RowId).select();
                    } else { // item got removed
                        this.$parent.dt.row('#' + item.DT_RowId).deselect();
                    }
                } else {
                    if (this.urlOnly) {
                        window.open(this.url, this.urlTarget);
                    } else {
                        window.location = this.url + '/' + (item.DT_RowId ?? item.id); // ? item.DT_RowId -> will not work for new entries
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
                rtl: false,
            });
        },
    },
    computed: {
        screenWidth() {
            return window.innerWidth;
        },
    },
}
</script>
<style>
.not-allowed {
    cursor: not-allowed;
    filter: opacity(50%);
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
</style>