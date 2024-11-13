<template>
    <div id="kanban_board_container"
         class="kanban_board_container"
    >
        <img v-if="kanban.medium_id !== null"
            class="kanban_board_wrapper p-0"
             alt="background image"
             :src="'/media/'+ kanban.medium_id + '?model=Kanban&model_id=' + kanban.id"
             style="object-fit: cover;
             position:absolute;"
        />
        <div id="kanban_board_wrapper"
             class="kanban_board_wrapper"
             :style="'background-color:' + kanbanColor"
        >
            <div class="pointer"
                :style="{ color: textColor }"
                style="float: left;
                position:fixed;
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
                v-model="statuses"
                v-bind="columnDragOptions"
                handle=".handle"
                :move="isLocked"
                @end="syncStatusMoved"
            >
                <div
                    v-for="(status, index) in statuses"
                    v-if="(status.visibility == true) || ($userId == status.owner_id )"
                    :key="'header_'+status.id"
                    class=" no-border pr-3"
                    :style="'float:left; width:' + itemWidth + 'px; height: calc(100vh - 425px)'"
                >
                    <KanbanStatus
                        :style="'width:' + (itemWidth-16) + 'px;'"
                        :kanban="kanban"
                        :status="status"
                        :editable="editable"
                        v-on:status-updated="handleStatusUpdatedWithoutWebsocket"
                        v-on:status-destroyed="handleStatusDestroyedWithoutWebsocket"
                        filter=".ignore"
                    />
                    <div v-if="(editable == true) && (status.editable == true) || ( $userId == status.owner_id ) "
                         v-show="newItem !== status.id"
                         :id="'kanbanTopItemCreateButton_' + index"
                         class="btn btn-flat py-2 w-100"
                         @click="openForm('item', status.id)">
                        <i class="text-white fa fa-2x fa-plus-circle"></i>
                    </div>
                    <div v-else
                         class="py-2 w-100">
                    </div>
                    <KanbanItemCreate
                        v-if="newItem === status.id"
                        :id="'kanbanItemCreate_' + index"
                        :status="status"
                        :item="item"
                        class="mt-2"
                        :style="'width:' + (itemWidth-16) + 'px;'"
                        :width="itemWidth"
                        v-on:item-added="handleItemAddedWithoutWebsocket"
                        v-on:item-updated=""
                        v-on:item-canceled="closeForm"
                        style="z-index: 2">
                    </KanbanItemCreate>
                    <div style="padding-bottom:100px; bottom:0; overflow-y:scroll; max-height: calc(100vh - 50px); z-index: 1"
                         :style="'width:' + itemWidth + 'px;'"
                         class="hide-scrollbars"
                    >
                        <draggable
                            class="flex-1 "
                            v-model="status.items"
                            v-bind="itemDragOptions"
                            :move="isLocked"
                            @end="syncItemMoved"
                            handle=".handle"
                        >
                            <transition-group
                                style="display:flex; flex-direction: column;"
                                :style="'width:' + itemWidth + 'px; height: calc(100vh + '+ columnHeight + 'px)'"
                                class="pr-3"
                                tag="span"
                            >
                                <!-- Items -->
                                <span
                                    v-for="(item, itemIndex) in status.items"
                                    :key="'transition_group-'+item.id"
                                >
                                    <KanbanItem
                                        v-if="(item.visibility == true && visiblefrom_to(item.visible_from, item.visible_until) == true) || ($userId == item.owner_id ) || ($userId == kanban.owner_id )"
                                        :editable="(status.editable == false && $userId != kanban.owner_id) ? false : editable"
                                        :commentable="kanban.commentable"
                                        :onlyEditOwnedItems="kanban.only_edit_owned_items"
                                        :allowCopy="kanban.allow_copy"
                                        :ref="'kanbanItemId' + item.id"
                                        :index="index + '_' + itemIndex"
                                        :item="item"
                                        :width="itemWidth"
                                        :kanban_owner_id="kanban.owner_id"
                                        v-on:item-destroyed="handleItemDestroyedWithoutWebsocket"
                                        v-on:item-updated="handleItemUpdatedWithoutWebsocket"
                                        v-on:item-edit=""
                                        v-on:sync="sync"
                                        filter=".ignore"
                                    />
                                </span>
                                <!--  ./Items -->
                            </transition-group>
                        </draggable>
                    </div>
                </div>
                <div v-if="editable"
                     class=" no-border  pr-2"
                     style="float:left;"
                     :style="'width:' + itemWidth + 'px;'"
                >
                    <KanbanStatus
                        :kanban="kanban"
                        :editable="editable"
                        :newStatus=true
                        v-on:status-added="handleStatusAddedWithoutWebsocket"
                    />
                </div>
            </draggable>
            <!-- ./Columns -->
            <div v-if="pusher === true"
                 class="card p-2"
                 style="position: fixed;right: 15px;bottom: 30px;"
            >
            <span class="pull-left">
                <i class="fa fa-user"></i> {{ this.usersOnline.length }}
            </span>
            </div>
        </div>
        </div>
        <KanbanIndexAddWidget
            :visible="false"
            v-can="'kanban_create'"
        />
        <Modal
            :id="'kanbanItemCopyModal'"
            css="primary"
            :title="trans('global.kanbanItem.copy')"
            :text="trans('global.kanbanItem.copy_helper')"
            ok_label="OK"
            v-on:ok="copyItem()"
        />
        <Modal
            :id="'kanbanStatusCopyModal'"
            css="primary"
            :title="trans('global.kanbanStatus.copy')"
            :text="trans('global.kanbanStatus.copy_helper')"
            ok_label="OK"
            v-on:ok="copyStatus()"
        />
    </div>
</template>

<script>
const draggable =
    () => import('vuedraggable');
const KanbanItem =
    () => import('./KanbanItem');
const KanbanItemCreate =
    () => import('./KanbanItemCreate');
const KanbanStatus =
    () => import('./KanbanStatus');
const Modal =
    () => import('./../uiElements/Modal');
import KanbanIndexAddWidget from "./KanbanIndexAddWidget";
//import draggable from "vuedraggable";
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
        statuses:{
            deep: true,
            handler(){
                //console.log('changed');
            }
        },
    },
    data() {
        return {
            kanbanColor: '',
            statuses: [],
            newItem: 0, // track the ID of the status we want to add to
            newStatus: 0,
            itemWidth: 320,
            columnHeight: -450,
            item: null,
            autoRefresh: false,
            refreshRate: 5000,
            usersOnline:[],
            copyId: undefined,
        };
    },
    methods: {
        toggleFullscreen(){
            if (document.fullscreenElement) {
                document.exitFullscreen();
                this.columnHeight= -450; //reset column Height
            } else {
                $('#kanban_board_container').get(0).requestFullscreen();
                this.columnHeight= -250; //adjust column Height
            }
        },
        visiblefrom_to(visible_from, visible_until){
            const now = moment().format("YYYY-MM-DD HH:mm:ss");

            return (now >= visible_from && now <= visible_until) ||
                (now >= visible_from && visible_until == null) ||
                (visible_from == null && now <= visible_until) ||
                (visible_from == null && visible_until == null);
        },
        sync(){
            axios.get("/kanbanStatuses/" + this.kanban.id + "/checkSync")
                .then(res => {
                    if (res.data.message !== 'uptodate'){
                        this.refreshRate = 5000;
                        this.statuses = res.data.message.statuses;
                    } else {
                        this.refreshRate +=1000; //slow down refreshing, if nothing happens
                    }
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        timer () {
            setTimeout(() => {
                if (this.autoRefresh){
                    this.sync();
                    this.timer()
                }
            }, this.refreshRate)
        },
        syncStatusMoved() {
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
                            this.handleItemMoved(res.data.message);
                        }
                    }
                })
                .catch(err => {
                    console.log(err);
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
        handleStatusAddedWithoutWebsocket(newStatus){
            if (this.pusher === false){
                this.sync();
                //this.handleStatusAdded(newStatus);
            }
        },
        handleStatusAdded(newStatus){
            /*if (this.statuses.findIndex(s => s.title = newStatus.title) != -1){
                this.infoNotification('Status existiert.');
            }*/
            newStatus['items'] = newStatus['items'] ?? []; // add items to prevent error if item is created without reloading page
            this.statuses.push(newStatus);
            //this.closeForm();
        },
        handleStatusUpdatedWithoutWebsocket(newStatus){
            if (this.pusher === false){
                this.handleStatusUpdated(newStatus);
            }
        },
        handleStatusUpdated(newStatus){
            const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should replace the item
                status => status.id === newStatus.id
            );

            for (const [key, value] of Object.entries(newStatus)) {
                this.statuses[statusIndex][key] = value;
            }
        },
        handleStatusDestroyedWithoutWebsocket(status){
            if (this.pusher === false){
                this.handleStatusDestroyed(status);
            }
        },
        handleStatusDestroyed(status){
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
        handleItemAddedWithoutWebsocket(newItem){
            if (this.pusher === false){
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
        handleItemMoved(statuses) {
            let newStatusOrder = [];

            statuses.forEach((status) => {
                let statusIndex = this.statuses.findIndex(
                    s => s.id === status.id
                );

                let newItemsOrder = [];
                status.items.forEach((item) => {
                    let tempItem = this.findItem(item);
                    tempItem.kanban_status_id = status.id;
                    newItemsOrder.push(tempItem);
                });

                let tempStatus = {};
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
        findItem(item){
                let foundItem = []
                this.statuses.forEach((status) => {
                    status.items.forEach((i) => {
                    if (i.id === item.id){
                        foundItem = i;
                        return; //break early;
                    }
                })
            });
                return foundItem;
        },
        handleItemDestroyedWithoutWebsocket(item){
            if (this.pusher === false){
                this.handleItemDestroyed(item);
            }
        },
        handleItemDestroyed(item){
            const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should add the item
                status => status.id === item.kanban_status_id
            );

            let index = this.statuses[statusIndex].items.findIndex(
                i => i.id === item.id
            );
            this.statuses[statusIndex].items.splice(index, 1);
        },
        handleItemUpdatedWithoutWebsocket(updatedItem){
            if (this.pusher === false){
                //console.log('update'+updatedItem);
                this.handleItemUpdated(updatedItem);
            }
        },
        handleItemUpdated(updatedItem){
            const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should replace the item
                status => status.id === updatedItem.kanban_status_id
            );
            const itemIndex = this.statuses[statusIndex].items.findIndex(   // Find the index of the item where we should replace the item
                item => item.id === updatedItem.id
            );

            for (const [key, value] of Object.entries(updatedItem)) {
                this.statuses[statusIndex].items[itemIndex][key] = value;       // Add updated item to our column
            }
        },
        handleItemCommentUpdated(updatedItem){
            const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should replace the item
                status => status.id === updatedItem.kanban_status_id
            );
            const itemIndex = this.statuses[statusIndex].items.findIndex(   // Find the index of the item where we should replace the item
                item => item.id === updatedItem.id
            );

            this.statuses[statusIndex].items[itemIndex]['comments'] = updatedItem.comments;       // Add updated item to our column
        },
        handleKanbanColorUpdated(color){
            this.kanbanColor = color;
        },

        startPusher(){
            if (this.pusher === true){
                this.$echo.join('Presence.App.Kanban.' + this.kanban.id)
                    .here((users) =>{
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
                    .joining((user)=> {
                        this.usersOnline.push(user);
                        //console.log({user}, 'joined');
                    })
                    .leaving((user)=> {
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
        isLocked(value){
            //console.log(value.draggedContext.element.locked );
            if (value.draggedContext.element.locked == true && this.$userId != value.draggedContext.element.owner_id) { //locked and not owner
                //console.log(false);
                return false;
            } else {
                //console.log(true);
                return true;
            }

        },
        hexToRgbA(hex){
            var c;
            if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
                c= hex.substring(1).split('');
                if(c.length== 3){
                    c= [c[0], c[0], c[1], c[1], c[2], c[2]];
                }
                c= '0x'+c.join('');
                return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+', 0.7)';
            }
            throw new Error('Bad Hex');
        },
        copyItem() {
            axios.get('/kanbanItems/' + this.copyId + '/copy')
                .then(response => this.handleItemAdded(response.data.message));
        },
        copyStatus() {
            axios.get('/kanbanStatuses/' + this.copyId + '/copy')
                .then(response => this.handleStatusAdded(response.data.message));
        },
    },
    mounted() {
        // Listen for the 'Kanban' event in the 'Presence.App.Kanban' presence channel
        this.startPusher();
        this.$eventHub.$on('reload_kanban_board', () => {
            this.sync()
        });
        this.$eventHub.$on('kanban-updated', () => {
            window.location.href = '/kanbans/'+this.kanban.id;
        });
        this.$eventHub.$on('item-updated', (item) => {
            this.handleItemUpdated(item);
        });
        this.$eventHub.$on('copy-id', (id) => {
            this.copyId = id;
        });
    },
    created () {
        this.statuses = this.kanban.statuses;

        if (this.kanban.color.length < 8) {
            this.kanbanColor = this.hexToRgbA(this.kanban.color)
        } else {
            this.kanbanColor = this.kanban.color;
        }


        if (this.kanban.auto_refresh === true){
            this.autoRefresh = true;
            this.timer();
        } else {
            this.autoRefresh = false;
        }

        this.$eventHub.$emit('showSearchbar');
    },
    computed: {
        textColor: function(){
            if(this.kanban.color == "" || this.kanban.color == null ) return;
            return this.$textcolor(this.kanban.color, '#333333');
        },
        columnDragOptions() {
            return {
                animation: 200,
                // checks if a mobile-browser is used and if true, add delay
                ...(/Mobi/i.test(window.navigator.userAgent) && {delay: 200}),
                group: "column-list",
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
        KanbanIndexAddWidget,
        Modal
    }
}
</script>
<style> /* not scoped since '.content-only' and 'sidebar-collapse' are outside of of this component */
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
    position: absolute;
    height: 100%;
    width: 100% !important;
    padding: 2rem;
    overflow-y: clip;
    overflow-x: overlay;
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
