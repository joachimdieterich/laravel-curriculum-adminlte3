<template>
    <div
        id="kanban_board_container"
        class="kanban_board_container"
    >
        <img v-if="currentKanban.medium_id"
            class="kanban_board_wrapper position-absolute p-0"
            style="object-fit: cover;"
            :src="'/media/' + currentKanban.medium_id + '?preview=true'"
            alt="background image"
        />
        <div
            class="position-absolute pointer"
            style="top: 10px; left: 10px; line-height: 1; z-index: 10;"
            :style="{ color: textColor }"
            @click="toggleFullscreen"
        >
            <i class="fa fa-expand"></i>
        </div>
        <div
            class="position-absolute pointer"
            style="top: 10px; right: 10px; line-height: 1; z-index: 10;"
            :style="{ color: textColor }"
            @click="toggleCollapseAll"
        >
            <i class="fa fa-angle-up"></i>
        </div>

        <div
            id="kanban_board_wrapper"
            class="kanban_board_wrapper position-relative"
            :style="'background-color: ' + currentKanban.color + 'B2;'"
        >
            <!-- Columns (Statuses) -->
            <draggable
                v-model="statuses"
                v-bind="columnDragOptions"
                :move="isLocked"
                @end="syncStatusMoved"
                handle=".handle"
                item-key="id"
                :emptyInsertThreshold="500"
                class="d-flex m-0 pr-0 h-100"
                style="width: max-content; gap: 16px; padding-right: 2rem"
            >
                <template #item="{ element: status , index }">
                    <span v-if="(status.visibility) || ($userId == status.owner_id)"
                        :key="'drag_status_' + status.id"
                        class="d-flex flex-column h-100"
                        :style="'width:' + itemWidth + 'px;'"
                    >
                        <KanbanStatus
                            :status="status"
                            :editable="editable"
                            :allow_copy="kanban.allow_copy"
                            :kanban_owner_id="kanban.owner_id"
                            :only_edit_owned_items="kanban.only_edit_owned_items"
                            :key="status.id"
                            filter=".ignore"
                        />
                        <div v-if="(editable && status.editable) || ($userId == status.owner_id)"
                            :id="'kanbanItemCreateButton_' + index"
                            class="btn btn-flat p-1 my-1 mx-auto"
                            @click="openItemModal(status.id)"
                        >
                            <i class="text-white fa fa-2x fa-plus-circle"></i>
                        </div>
                        <div v-else class="py-2"></div>
                        <div
                            class="hide-scrollbars"
                            style="overflow-y: scroll;"
                        >
                            <draggable
                                :list="status.items"
                                v-bind="itemDragOptions"
                                :move="isLocked"
                                @end="syncItemMoved"
                                handle=".handle"
                                item-key="kanban_status_id"
                                :component-data="{ name: 'fade' }"
                            >
                                <template
                                    #item="{ element: item, itemIndex }"
                                    :style="'width:' + itemWidth + 'px;'"
                                    class="d-flex flex-column pr-3"
                                >
                                    <span :key="'item_' + item.id">
                                        <KanbanItem v-if="(item.visibility && visiblefrom_to(item.visible_from, item.visible_until) == true)
                                                || ($userId == item.owner_id)
                                                || ($userId == kanban.owner_id)"
                                            :key="item.id"
                                            :allow_copy="kanban.allow_copy"
                                            :editable="(status.editable == false && $userId != kanban.owner_id) ? false : editable"
                                            :commentable="currentKanban.commentable"
                                            :only_edit_owned_items="kanban.only_edit_owned_items"
                                            :ref="'kanbanItemId' + item.id"
                                            :index="status.id + '_' + item.id"
                                            :item="item"
                                            :width="itemWidth"
                                            :kanban_owner_id="kanban.owner_id"
                                            v-on:item-edit=""
                                            v-on:sync="sync"
                                            filter=".ignore"
                                        />
                                    </span>
                                </template>
                            </draggable>
                        </div>
                    </span>
                </template>
                <template #footer>
                    <div v-if="editable"
                        class="no-border float-left pr-2"
                        :style="'width:' + itemWidth + 'px;'"
                    >
                        <KanbanStatus :newStatus="true"/>
                    </div>
                </template>
            </draggable>
        </div>

        <Teleport to=".content">
            <KanbanModal/>
            <KanbanItemModal/>
            <KanbanStatusModal :kanban="kanban"/>
            <MediumModal/>
            <SubscribeModal/>
            <ConfirmModal
                :showConfirm="show_item_copy"
                :title="trans('global.kanbanItem.copy')"
                :description="trans('global.kanbanStatus.copy_helper')"
                css="primary"
                @close="show_item_copy = false"
                @confirm="() => {
                    this.show_item_copy = false;
                    this.copyItem();
                }"
            />
            <ConfirmModal
                :showConfirm="show_status_copy"
                :title="trans('global.kanbanStatus.copy')"
                :description="trans('global.kanbanStatus.copy_helper')"
                css="primary"
                @close="show_status_copy = false"
                @confirm="() => {
                    this.show_status_copy = false;
                    this.copyStatus();
                }"
            />
        </Teleport>
        <Teleport to="#customTitle">
            <small>{{ kanban.title }}</small>
            <a v-if="kanban.owner_id == $userId || checkPermission('is_admin')"
                class="btn btn-flat text-secondary px-2 mx-1"
                @click="editKanban(kanban)"
            >
                <i class="fa fa-pencil-alt"></i>
            </a>
    
            <a v-if="kanban.owner_id == $userId || checkPermission('is_admin')"
                class="btn btn-flat text-secondary px-2 mx-1"
                @click="share()"
            >
                <i class="fa fa-share-alt"></i>
            </a>
    
            <a
                :href="'/export_csv/' + kanban.id"
                class="btn text-secondary px-1 ml-2"
            >
                <i class="fa fa-file-csv"></i>
            </a>
    
            <a
                :href="'/export_pdf/' + kanban.id"
                class="btn text-secondary px-1 ml-1"
            >
                <i class="fa fa-file-pdf"></i>
            </a>

            <p class="h6">{{ trans('global.owner') }}: {{ kanban.owner.firstname + ' ' + kanban.owner.lastname }}</p>
        </Teleport>
    </div>
</template>
<script>
import draggable from "vuedraggable";
import KanbanItem from "../kanbanItem/KanbanItem.vue";
import KanbanItemModal from "../kanbanItem/KanbanItemModal.vue";
import KanbanStatus from "./KanbanStatus.vue";
import KanbanStatusModal from "./KanbanStatusModal.vue";
import MediumModal from "../media/MediumModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import KanbanModal from "../kanban/KanbanModal.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        kanban: {
            type: Object,
            default: null,
        },
        editable: {
            type: Boolean,
            default: true,
        },
        pusher: {
            type: Boolean,
            default: false,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            currentKanban: {},
            statuses: [],
            newItem: 0, // track the ID of the status we want to add to
            newStatus: 0,
            itemWidth: 320,
            item: null,
            copy_id: null,
            show_item_copy: false,
            show_status_copy: false,
            autoRefresh: false,
            refreshRate: 5000,
            usersOnline: [],
        };
    },
    methods: {
        editKanban(kanban) {
            this.globalStore?.showModal('kanban-modal', kanban);
        },
        toggleFullscreen() {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                document.querySelector('.content').requestFullscreen();
                // $('#kanban_board_container').get(0).requestFullscreen();
            }
        },
        toggleCollapseAll(e) {
            const collapse = e.target.parentElement.classList.toggle('collapsed') ? 'hide' : 'show';
            $('#kanban_board_wrapper .card-body').collapse(collapse);
        },
        share() {
            this.globalStore?.showModal('subscribe-modal', {
                modelId: this.currentKanban.id,
                modelUrl: 'kanban',
                shareWithUsers: true,
                shareWithGroups: true,
                shareWithOrganizations: true,
                shareWithToken: true,
                canEditCheckbox: true,
            });
        },
        visiblefrom_to(visible_from, visible_until) {
            const now = moment().format("YYYY-MM-DD HH:mm:ss");

            return (now >= visible_from && now <= visible_until) ||
                (now >= visible_from && visible_until == null) ||
                (visible_from == null && now <= visible_until) ||
                (visible_from == null && visible_until == null);
        },
        sync() {
            axios.get("/kanbanStatuses/" + this.currentKanban.id + "/checkSync")
                .then(res => {
                    if (res.data.message !== 'uptodate') {
                        this.refreshRate = 5000;
                        this.statuses = res.data.message.statuses;
                    } else {
                        this.refreshRate += 1000; //slow down refreshing, if nothing happens
                    }
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        timer() {
            setTimeout(() => {
                if (this.autoRefresh){
                    this.sync();
                    this.timer()
                }
            }, this.refreshRate)
        },
        syncStatusMoved() {
            console.log('syncStatusMoved');
            this.sendChange("/kanbanStatuses/sync");
        },
        syncItemMoved() {
            this.sendChange("/kanbanItems/sync");
        },
        sendChange(url) {
            const cols = this.statuses
                .map((status) => {      //only send required data
                    return {
                        id: status.id,
                        kanban_id: status.kanban_id,
                        order_id: status.order_id,
                        items: status.items.map((item) => {
                            return {
                                id: item.id,
                                kanban_status_id: item.kanban_status_id,
                                order_id: item.order_id,
                            }
                        }),
                    }
                });

            axios.put(url, {columns: cols})
                .then(res => { // Tell the parent component we've added a new task and include it
                    if (this.pusher === false) {
                        if (url == '/kanbanStatuses/sync') {
                            this.handleStatusMoved(res.data.message.statuses);
                        } else {
                            this.handleItemMoved(res.data.message);
                        }
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        },
        openItemModal(status_id) {
            this.globalStore?.showModal('kanban-item-modal', {
                item: {
                    kanban_id: this.currentKanban.id,
                    kanban_status_id: status_id,
                },
                method: 'post',
            });
        },
        copyStatus() {
            axios.get('/kanbanStatuses/' + this.copy_id + '/copy')
                .then(response => this.handleStatusAdded(response.data));
        },
        copyItem() {
            axios.get('/kanbanItems/' + this.copy_id + '/copy')
                .then(response => this.handleItemAdded(response.data));
        },
        handleStatusAdded(newStatus) {
            // add items to prevent error if item is created without reloading page
            if (newStatus['items'] == undefined) newStatus['items'] = [];
            this.statuses.push(newStatus);
        },
        handleStatusUpdated(newStatus) {
            let status = this.statuses.find(s => s.id === newStatus.id);

            Object.assign(status, newStatus);
        },
        handleStatusDeleted(status) {
            let index = this.statuses.indexOf(status);
            this.statuses.splice(index, 1);
        },
        handleStatusMoved(newStatusOrder) {
            let newStatusesOrderTemp = [];

            newStatusOrder.forEach((status) => {
                let statusIndex = this.statuses.findIndex(
                    s => s.id === status.id
                );
                newStatusesOrderTemp.push(this.statuses[statusIndex]);
            });
            this.statuses = newStatusesOrderTemp;
        },
        handleItemAdded(newItem) {
            // add an item to the correct column in our list
            const statusIndex = this.statuses.findIndex(
                status => status.id === newItem.kanban_status_id
            );
            // Add newly created item to our column
            this.statuses[statusIndex].items.push(newItem);
        },
        handleItemMoved(columns) {
            let newStatusOrder = [];

            columns.forEach((status) => {
                let statusIndex = this.statuses.findIndex(
                    s => s.id === status.id
                );

                let newItemsOrder = [];
                status.items.forEach((item) => {
                    newItemsOrder.push(this.findItem(item));
                });

                let tempStatus = [];
                for (const [key, value] of Object.entries(this.statuses[statusIndex])) {
                    if (key !== 'items'){
                        tempStatus[key] = value;
                    } else {
                        tempStatus['items'] = newItemsOrder;
                    }
                }
                newStatusOrder.push(tempStatus);
            })
            this.statuses = newStatusOrder;
        },
        findItem(item) {
            let foundItem = []
            this.statuses.forEach((status) => {
                status.items.forEach((i) => {
                    if (i.id === item.id) {
                        foundItem = i;
                        return; //break early;
                    }
                })
            });
            return foundItem;
        },
        handleItemDeleted(item) {
            // Find the index of the status where we should delete the item
            const statusIndex = this.statuses.findIndex(
                status => status.id === item.kanban_status_id
            );

            let index = this.statuses[statusIndex].items.findIndex(
                i => i.id === item.id
            );
            this.statuses[statusIndex].items.splice(index, 1);
        },
        handleItemUpdated(updatedItem) {
            // Find the index of the status where we should replace the item
            const statusIndex = this.statuses.findIndex(
                status => status.id === updatedItem.kanban_status_id
            );
            // Find the index of the item where we should replace the item
            let item = this.statuses[statusIndex].items.find(
                item => item.id === updatedItem.id
            );

            Object.assign(item, updatedItem);
        },
        handleItemCommentUpdated(updatedItem) {
            // Find the index of the status where we should replace the item
            const statusIndex = this.statuses.findIndex(
                status => status.id === updatedItem.kanban_status_id
            );
            // Find the index of the item where we should replace the item
            const itemIndex = this.statuses[statusIndex].items.findIndex(
                item => item.id === updatedItem.id
            );

            // Add updated item to our column
            this.statuses[statusIndex].items[itemIndex]['comments'] = updatedItem.comments;
        },
        startPusher() {
            if (this.pusher === true) {
                this.$echo.join('Presence.App.Kanban.' + this.kanban.id)
                    .here((users) => {
                        this.usersOnline = [...users];
                    })
                    .listen('.kanbanStatusAdded', (payload) => {
                        this.handleStatusAdded(payload.message);
                    })
                    .listen('.kanbanStatusUpdated', (payload) => {
                        this.handleStatusUpdated(payload.message);
                    })
                    .listen('.kanbanStatusMoved', (payload) => {
                        this.handleStatusMoved(payload.message.statuses);
                    })
                    .listen('.kanbanStatusDeleted', (payload) => {
                        this.handleStatusDeleted(payload.message);
                    })
                    .listen('.kanbanItemAdded', (payload) => {
                        this.handleItemAdded(payload.message);
                    })
                    .listen('.kanbanItemUpdated', (payload) => {
                        this.handleItemUpdated(payload.message);
                    })
                    .listen('.kanbanItemMoved', (payload) => {
                        this.handleItemMoved(payload.message);
                    })
                    .listen('.kanbanItemReload', (payload) => {
                        this.handleItemUpdated(payload.message);
                    })
                    .listen('.kanbanItemDeleted', (payload) => {
                        this.handleItemDeleted(payload.message);
                    })
                    .listen('.kanbanColorUpdated', (payload) => {
                        this.currentKanban.color = payload.message;
                    })
                    .listen('.kanbanItemCommentUpdated', (payload) => {
                        //console.log('kanbanItemCommentUpdated');
                        this.handleItemCommentUpdated(payload.message);
                    })
                    .joining((user) => {
                        this.usersOnline.push(user);
                        //console.log({user}, 'joined');
                    })
                    .leaving((user) => {
                        //console.log({user}, 'leaving');
                        this.usersOnline.filter((userOnline) => userOnline.id !== user.id);
                    });
            }
        },
        successNotification(message) {
            this.$toast.success(message, {
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
        infoNotification(message) {
            this.$toast.info(message, {
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
        isLocked(value) {
            if (value.draggedContext.element.locked == true && this.$userId != value.draggedContext.element.owner_id) { //locked and not owner
                return false;
            } else {
                return true;
            }
        },
    },
    mounted() {
        this.currentKanban = this.kanban;

        // Listen for the 'Kanban' event in the 'Presence.App.Kanban' presence channel
        this.startPusher();
        this.$eventHub.on('reload_kanban_board', () => {
            this.sync()
        });
        this.$eventHub.on('kanban-updated', (updatedKanban) => {
            Object.assign(this.currentKanban, updatedKanban);
        });
        // STATUS Events
        this.$eventHub.on('kanban-status-added', (status) => {
            this.handleStatusAdded(status);
        });
        this.$eventHub.on('kanban-status-updated', (status) => {
            this.handleStatusUpdated(status);
        });
        this.$eventHub.on('kanban-status-deleted', (status) => {
            this.handleStatusDeleted(status);
        });
        // ITEM Events
        this.$eventHub.on('kanban-item-added', (item) => {
            this.handleItemAdded(item);
        });
        this.$eventHub.on('kanban-item-updated', (item) => {
            this.handleItemUpdated(item);
        });
        this.$eventHub.on('kanban-item-deleted', (item) => {
            this.handleItemDeleted(item);
        });
        // COPY Events
        this.$eventHub.on('kanban-show-copy', data => {
            this.copy_id = data.id;
            if (data.type == 'item') {
                this.show_item_copy = true;
            } else if (data.type == 'status') {
                this.show_status_copy = true;
            }
        });
    },
    created() {
        this.statuses = this.kanban.statuses;

        if (this.kanban.auto_refresh) {
            this.autoRefresh = true;
            this.timer();
        } else {
            this.autoRefresh = false;
        }
    },
    computed: {
        textColor: function() {
            if (this.currentKanban.color == "" || this.currentKanban.color == null) return;
            return this.$textcolor(this.currentKanban.color, '#333333');
        },
        columnDragOptions() {
            return {
                animation: 200,
                // checks if a mobile-browser is used and if true, add delay
                ...(/Mobi/i.test(window.navigator.userAgent) && {delay: 200}),
                group: "columns",
                dragClass: "status-drag",
                fallbackTolerance: 5,
                disabled: !this.editable
            };
        },
        itemDragOptions() {
            return {
                animation: 200,
                ...(/Mobi/i.test(window.navigator.userAgent) && {delay: 200}),
                group: "item-list",
                dragClass: "status-drag",
                fallbackTolerance: 5,
                disabled: !this.editable
            };
        },
        kanbanWidth() {
            return "width: "+ ((this.statuses.length) * this.itemWidth +this.itemWidth) +"px";
        },
    },
    components: {
        KanbanStatus,
        draggable,
        KanbanItem,
        KanbanItemModal,
        MediumModal,
        SubscribeModal,
        KanbanModal,
        KanbanStatusModal,
        ConfirmModal,
    },
}
</script>
<style scoped>
.status-drag {
    transition: transform 0.5s;
    transition-property: all;
}
.kanban_board_container {
    background-color: #fff;
    position: relative;
    width: 100%;
}
.kanban_board_wrapper {
    height: 100%;
    width: 100%;
    padding: 2rem;
    overflow-x: overlay;
    overflow-y: clip;
}
@media (max-width: 991px) {
    .kanban_board_container {
        width: 100vw;
        margin-left: -1rem;
    }
}
.fa-angle-up { transition: 0.4s transform; }
.collapsed .fa-angle-up { transform: rotate(-180deg) !important; }
</style>