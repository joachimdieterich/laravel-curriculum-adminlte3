<template>
    <div
        id="kanban_board_container"
        class="kanban_board_container"
    >
        <img v-if="kanban.medium_id !== null"
            class="kanban_board_wrapper position-absolute p-0"
            style="object-fit: cover;"
            :src="'/media/' + kanban.medium_id + '?model=Kanban&model_id=' + kanban.id"
            alt="background image"
        />
        <div
            id="kanban_board_wrapper"
            class="kanban_board_wrapper position-relative"
            :style="'background-color:' + kanbanColor"
        >
            <div
                class="position-absolute pointer"
                style="top: 10px; left: 10px;"
                :style="{ color: textColor }"
                @click="toggleFullscreen"
            >
                <i class="fa fa-expand position-fixed"></i>
            </div>
            <div
                class="position-absolute pointer"
                style="top: 10px; right: 10px;"
                :style="{ color: textColor }"
                data-toggle="collapse"
                data-target="#kanban_board_wrapper .card-body"
                aria-expanded="true"
            >
                <i
                    class="fa fa-angle-up position-fixed"
                    style="transform: translateX(-100%);"
                ></i>
            </div>

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
                            :kanban="kanban"
                            :status="status"
                            :editable="editable"
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
                                            :editable="(status.editable == false && $userId != kanban.owner_id) ? false : editable"
                                            :commentable="kanban.commentable"
                                            :onlyEditOwnedItems="kanban.only_edit_owned_items"
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
                        <KanbanStatus
                            :kanban="kanban"
                            :editable="editable"
                            :newStatus=true
                        />
                    </div>
                </template>
            </draggable>
        </div>
    </div>
    <Teleport to="body">
        <MediumModal/>
<!--        <MediumModal
            subscribable_type="App\\Kanban"
            :subscribable_id="kanban.id"
            :show="mediumStore.getShowMediumModal"
            @close="mediumStore.setShowMediumModal(false)"
        ></MediumModal>-->
        <KanbanModal/>
        <KanbanItemModal/>
        <KanbanStatusModal :kanban="kanban"/>
        <SubscribeModal/>
    </Teleport>
    <teleport v-if="$userId == kanban.owner_id"
        to="#customTitle"
    >
        <small>{{ kanban.title }} </small>
            <a
                class="btn btn-flat"
                @click="editKanban(kanban)"
            >
                <i class="fa fa-pencil-alt text-secondary"></i>
            </a>

            <button v-if="$userId == kanban.owner_id"
                v-permission="'kanban_create'"
                class="btn btn-flat"
                @click="share()"
            >
                <i class="fa fa-share-alt text-secondary"></i>
            </button>

            <a
                :href="'/export_csv/' + kanban.id"
                class="btn p-0"
            >
                <i class="fa fa-file-csv text-secondary"></i>
            </a>

            <a
                :href="'/export_pdf/' + kanban.id"
                class="btn p-0"
            >
                <i class="fa fa-file-pdf text-secondary"></i>
            </a>
    </teleport>
</template>
<script>
import draggable from "vuedraggable";
import KanbanItem from "../kanbanItem/KanbanItem.vue";
import KanbanItemModal from "../kanbanItem/KanbanItemModal.vue";
import KanbanStatus from "./KanbanStatus.vue";
import KanbanStatusModal from "./KanbanStatusModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import KanbanModal from "../kanban/KanbanModal.vue";
import {useGlobalStore} from "../../store/global";
import MediumModal from "../media/MediumModal.vue";

export default {
    props: {
        kanban: Object,
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
            kanbanColor: '',
            statuses: [],
            newItem: 0, // track the ID of the status we want to add to
            newStatus: 0,
            itemWidth: 320,
            item: null,
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
                $('#kanban_board_container').get(0).requestFullscreen();
            }
        },
        share() {
            this.globalStore?.showModal('subscribe-modal', {
                modelId: this.kanban.id,
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
            axios.get("/kanbanStatuses/" + this.kanban.id + "/checkSync")
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
                    kanban_id: this.kanban.id,
                    kanban_status_id: status_id,
                },
                method: 'post',
            });
        },
        handleStatusAdded(newStatus) {
            // add items to prevent error if item is created without reloading page
            newStatus['items'] = [];
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
            // Find the index of the status where we should add the item
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
        handleKanbanColorUpdated(color) {
            this.kanbanColor = color;
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
                        this.handleKanbanColorUpdated(payload.message);
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
        hexToRgbA(hex){
            let c;
            if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
                c = hex.substring(1).split('');
                if (c.length == 3) {
                    c = [c[0], c[0], c[1], c[1], c[2], c[2]];
                }
                c = '0x'+c.join('');
                return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+', 0.7)';
            }
            throw new Error('Bad Hex');
        }
    },
    mounted() {
        // Listen for the 'Kanban' event in the 'Presence.App.Kanban' presence channel
        this.startPusher();
        this.$eventHub.on('reload_kanban_board', () => {
            this.sync()
        });
        this.$eventHub.on('kanban-updated', () => {
            window.location.href = '/kanbans/'+this.kanban.id;
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
    },
    created() {
        this.statuses = this.kanban.statuses;

        if (this.kanban.color.length < 8) {
            this.kanbanColor = this.hexToRgbA(this.kanban.color)
        } else {
            this.kanbanColor = this.kanban.color;
        }

        if (this.kanban.auto_refresh === true) {
            this.autoRefresh = true;
            this.timer();
        } else {
            this.autoRefresh = false;
        }
    },
    computed: {
        textColor: function() {
            if (this.kanban.color == "" || this.kanban.color == null) return;
            return this.$textcolor(this.kanban.color, '#333333');
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
        MediumModal,
        KanbanStatus,
        draggable,
        KanbanItem,
        KanbanItemModal,
        SubscribeModal,
        KanbanModal,
        KanbanStatusModal
    }
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
    height: calc(100vh - 205px);
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
.collapsed .fa-angle-up { transform: translateX(-100%) rotate(-180deg) !important; }
</style>
