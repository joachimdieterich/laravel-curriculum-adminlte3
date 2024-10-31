<template>
    <div
        id="kanban_board_container"
        class="kanban_board_container"
    >
        <img
            v-if="kanban.medium_id !== null"
            class="kanban_board_wrapper p-0"
            alt="background image"
            :src="'/media/'+ kanban.medium_id + '?model=Kanban&model_id=' + kanban.id"
            style="object-fit: cover;
            position:absolute;"
        />
        <div
            id="kanban_board_wrapper"
            class="kanban_board_wrapper"
            :style="'background-color:' + kanbanColor"
        >
            <div
                class="pointer"
                :style="{ color: textColor }"
                style="float: left;
                margin-top: -25px;
                margin-left: -20px;"
                @click="toggleFullscreen"
            >
                <i class="fa fa-expand"></i>
            </div>
            <div
                :style="kanbanWidth "
                class="m-0"
            >
                <!-- Columns (Statuses) -->
                <draggable
                    :list="statuses"
                    v-bind="columnDragOptions"
                    :move="isLocked"
                    @end="syncStatusMoved"
                    handle=".handle"
                    itemKey="id"
                    :emptyInsertThreshold="500"
                >
                    <template
                        #item="{ element: status , index }"
                    >
                        <span
                            v-if="(status.visibility) || ($userId == status.owner_id)"
                            :key="'drag_status_' + status.id"
                            class=" no-border pr-3"
                            :style="'float:left; width:' + itemWidth + 'px;'"
                        >
                            <KanbanStatus
                                :kanban="kanban"
                                :status="status"
                                :editable="editable"
                                v-on:kanban-status-updated="handleStatusUpdatedWithoutWebsocket"
                                v-on:kanban-status-deleted="handleStatusDestroyedWithoutWebsocket"
                                filter=".ignore"
                            />
                            <div
                                style="margin-top: 15px; bottom: 0; overflow-y: scroll; z-index: 1"
                                :style="'width:' + itemWidth + 'px;'"
                                class="hide-scrollbars pr-3"
                            >
                                <draggable
                                    :list="status.items"
                                    v-bind="itemDragOptions"
                                    :move="isLocked"
                                    @end="syncItemMoved"
                                    handle=".handle"
                                    item-key="kanban_status_id"
                                    :component-data="{name:'fade'}"
                                >
                                    <template
                                        #item="{ element: item, itemIndex }"
                                        style="display:flex; flex-direction: column;"
                                        :style="'width:' + itemWidth + 'px;'"
                                        class="pr-3"
                                    >
                                        <span :key="'item_' + item.id">
                                            <KanbanItem
                                                v-if=" (item.visibility && visiblefrom_to(item.visible_from, item.visible_until) == true)
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
                                                style="min-height: 150px"
                                                v-on:item-destroyed="handleItemDestroyedWithoutWebsocket"
                                                v-on:item-updated="handleItemUpdatedWithoutWebsocket"
                                                v-on:item-edit=""
                                                v-on:sync="sync"
                                                filter=".ignore"
                                            />
                                        </span>

                                    </template>
                                </draggable>
                                <KanbanItemCreate
                                    v-if="newItem === status.id"
                                    :id="'kanbanItemCreate_' + index"
                                    :status="status"
                                    :item="item"
                                    :width="itemWidth"
                                    v-on:item-added="handleItemAddedWithoutWebsocket"
                                    v-on:item-updated=""
                                    v-on:item-canceled="closeForm"
                                    style="z-index: 2">
                                </KanbanItemCreate>
                                <div v-if="(editable == true) && (status.editable == true) || ($userId == status.owner_id)"
                                     v-show="newItem !== status.id"
                                     :id="'kanbanItemCreateButton_' + index"
                                     class="btn btn-flat py-0 w-100"
                                     style="margin-bottom: 1rem;"
                                     @click="openForm('item', status.id)">
                                    <i class="text-white fa fa-2x fa-plus-circle"></i>
                                </div>
                            </div>
                        </span>
                    </template>
                    <template #footer>
                        <div v-if="editable"
                             class=" no-border  pr-2"
                             style="float:left;"
                             :style="'width:' + itemWidth + 'px;'">
                            <KanbanStatus
                                :kanban="kanban"
                                :editable="editable"
                                :newStatus=true
                                v-on:kanban-status-added="handleStatusAddedWithoutWebsocket"
                            />
                        </div>
                    </template>
                </draggable>
            </div>
        </div>
    </div>
    <Teleport to="body">
        <KanbanModal></KanbanModal>
        <KanbanStatusModal
        :kanban="kanban"></KanbanStatusModal>
        <SubscribeModal></SubscribeModal>
    </Teleport>
    <teleport
        v-if="$userId == kanban.owner_id"
        to="#customTitle">
        <small>{{ kanban.title }} </small>
            <a class="btn btn-flat"
               @click="editKanban(kanban)"
            >
                <i class="fa fa-pencil-alt text-secondary"></i>
            </a>

            <button
                v-permission="'kanban_create'"
                v-if="$userId == kanban.owner_id"
                class="btn btn-flat"
                @click="share()">
                <i class="fa fa-share-alt text-secondary"></i>
            </button>

            <a :href="'/export_csv/' + kanban.id"
               class="btn p-0">
                <i class="fa fa-file-csv text-secondary"></i>
            </a>

            <a :href="'/export_pdf/' + kanban.id"
               class="btn p-0">
                <i class="fa fa-file-pdf text-secondary"></i>
            </a>
    </teleport>

</template>

<script>
import draggable from "vuedraggable";
import KanbanItem from "../kanbanItem/KanbanItem.vue";
import KanbanItemCreate from "../kanbanItem/KanbanItemCreate.vue";
import KanbanStatus from "./KanbanStatus.vue";
import KanbanStatusModal from "./KanbanStatusModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import KanbanModal from "../kanban/KanbanModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        'kanban': Object,
        'editable': {
            type: Boolean,
            default: true
        },
        'pusher': {
            type: Boolean,
            default: false
        },
        'search': ''
    },
    watch: {
        statuses: {
            deep: true,
            handler() {
                //console.log('changed');
            }
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
            usersOnline:[],
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
                'modelId': this.kanban.id,
                'modelUrl': 'kanban',
                'shareWithUsers': true,
                'shareWithGroups': true,
                'shareWithOrganizations': true,
                'shareWithToken': true,
                'canEditCheckbox': true
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
            this.sendChange("/kanbanItems/sync")
        },
        sendChange(url) {
            const cols = this.statuses
                .map((status) => {      //only send required data
                    return {
                        'id': status.id,
                        'kanban_id': status.kanban_id,
                        'order_id': status.order_id,
                        'items': status.items.map((item) => {
                            return {
                                'id': item.id,
                                'kanban_status_id': item.kanban_status_id,
                                'order_id': item.order_id,
                            }
                        })
                    }
                });
            axios.put(url, {columns: cols})
                .then(res => { // Tell the parent component we've added a new task and include it
                    if (this.pusher === false){
                        if (url == '/kanbanStatuses/sync'){
                            this.handleStatusMoved(res.data.message.statuses);
                        } else {
                            this.handleItemMoved(res.data.message.statuses);
                        }
                    }
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        openForm(type, value = 1) {
            this.item = null;
            if (type === 'status'){
                this.newStatus = value;
            } else {
                this.newItem = value;
            }
        },
        // reset the statusId and close form
        closeForm() {
            this.newStatus = 0;
            this.newItem = 0;
        },
        handleStatusAddedWithoutWebsocket(newStatus) {
            if (this.pusher === false) {
                this.sync();
                // this.handleStatusAdded(newStatus);
            }
        },
        handleStatusAdded(newStatus) {
            // add items to prevent error if item is created without reloading page
            newStatus['items'] = [];
            this.statuses.push(newStatus);
        },
        handleStatusUpdatedWithoutWebsocket(newStatus) {
            if (this.pusher === false) {
                this.handleStatusUpdated(newStatus);
            }
        },
        handleStatusUpdated(newStatus) {
            const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should replace the item
                status => status.id === newStatus.id
            );

            for (const [key, value] of Object.entries(newStatus)) {
                this.statuses[statusIndex][key] = value;
            }
        },
        handleStatusDestroyedWithoutWebsocket(status) {
            if (this.pusher === false) {
                this.handleStatusDestroyed(status);
            }
        },
        handleStatusDestroyed(status) {
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
        handleItemAddedWithoutWebsocket(newItem) {
            if (this.pusher === false) {
                this.handleItemAdded(newItem);
            }
        },
        handleItemAdded(newItem) {      // add an item to the correct column in our list
            const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should replace the item
                status => status.id === newItem.kanban_status_id
            );
            this.statuses[statusIndex].items.push(newItem);       // Add newly created item to our column

            this.closeForm();                                     // Reset and close the AddItemForm
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
        handleItemDestroyedWithoutWebsocket(item) {
            if (this.pusher === false) {
                this.handleItemDestroyed(item);
            }
        },
        handleItemDestroyed(item) {
            // Find the index of the status where we should add the item
            const statusIndex = this.statuses.findIndex(
                status => status.id === item.kanban_status_id
            );

            let index = this.statuses[statusIndex].items.findIndex(
                i => i.id === item.id
            );
            this.statuses[statusIndex].items.splice(index, 1);
        },
        handleItemUpdatedWithoutWebsocket(updatedItem) {
            if (this.pusher === false) {
                //console.log('update'+updatedItem);
                this.handleItemUpdated(updatedItem);
            }
        },
        handleItemUpdated(updatedItem) {
            // Find the index of the status where we should replace the item
            const statusIndex = this.statuses.findIndex(
                status => status.id === updatedItem.kanban_status_id
            );
            // Find the index of the item where we should replace the item
            const itemIndex = this.statuses[statusIndex].items.findIndex(
                item => item.id === updatedItem.id
            );

            for (const [key, value] of Object.entries(updatedItem)) {
                // Add updated item to our column
                this.statuses[statusIndex].items[itemIndex][key] = value;
            }
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
                        this.handleStatusDestroyed(payload.message);
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
                        this.handleItemDestroyed(payload.message);
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

        this.$eventHub.on('kanban-status-added', (newStatus) => {
            this.handleStatusAdded(newStatus);
        });

        this.$eventHub.on('kanban-item-updated', (item) => {
            this.handleItemUpdated(item);
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

        this.$eventHub.emit('showSearchbar');
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
        KanbanStatus,
        draggable,
        KanbanItem,
        KanbanItemCreate,
        SubscribeModal,
        KanbanModal,
        KanbanStatusModal
    }
}
</script>
<style> /* not scoped since '.content-only' and 'sidebar-collapse' are outside of this component */
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
.content-only .kanban_board_container { width: calc(100vw - 1rem); }
.kanban_board_wrapper {
    position:absolute;
    height: 100%;
    width: 100%;
    padding: 2rem;
    overflow:auto;
}
@media (max-width: 991px) {
    .kanban_board_container { width: calc(100vw - 30px) !important; }
}
@media (max-width: 767.98px) {
    .sidebar-collapse .kanban_board_container,
    .content-only .kanban_board_container,
    .kanban_board_container
    {
        width: 100vw !important;
        margin-left: -1rem;
    }
}
</style>
