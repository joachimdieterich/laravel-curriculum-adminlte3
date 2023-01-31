<template>
    <div
        :style="kanbanWidth"
        class="m-0">
        <!-- Columns (Statuses) -->
        <draggable
            v-model="statuses"
            v-bind="columnDragOptions"
            :options="{disabled: !editable}"
            @end="syncStatusMoved">
            <div
                v-for="(status, index) in statuses"
                :key="'header_'+status.id"
                class=" no-border pr-3"
                :style="'float:left; width:' + itemWidth + 'px;'">
                 <KanbanStatus
                     :kanban_id="status.kanban_id"
                     :status="status"
                     :editable="editable"
                     v-on:status-updated="handleStatusUpdatedWithoutWebsocket"
                     v-on:status-destroyed="handleStatusDestroyedWithoutWebsocket"
                 />
                <div style="margin-top:15px; bottom:0;overflow-y:scroll; z-index: 1"
                     :style="'width:' + itemWidth + 'px;'"
                      class="hide-scrollbars">
                    <draggable
                        class="flex-1 overflow-hidden hide-scrollbars"
                        v-model="status.items"
                        v-bind="itemDragOptions"
                        @end="syncItemMoved"
                        :options="{disabled: !editable}"
                        filter=".ignore">
                        <transition-group
                            style="display:flex; flex-direction: column;"
                            :style="'width:' + itemWidth + 'px;'"
                            class="pr-3"
                            tag="span">
                            <!-- Items -->
                            <span
                                v-for="(item, itemIndex) in status.items"
                                :key="'transition_group-'+item.id">
                                 <KanbanItem
                                    :editable="editable"
                                    :commentable="kanban.commentable"
                                     :ref="'kanbanItemId' + item.id"
                                     :index="index + '_' + itemIndex"
                                     :item="item"
                                     :width="itemWidth"
                                     v-on:item-destroyed="handleItemDestroyedWithoutWebsocket"
                                     v-on:item-updated="handleItemUpdatedWithoutWebsocket"
                                     v-on:item-edit=""/>
                            </span>
                            <!--  ./Items -->
                        </transition-group>
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
                    <div v-show="newItem !== status.id" v-if="editable"
                         :id="'kanbanItemCreateButton_' + index"
                         class="btn btn-flat py-0 w-100"
                         @click="openForm('item', status.id)">
                        <i class="text-white fa fa-2x fa-plus-circle"></i>
                    </div>
                </div>
            </div>
            <div v-if="editable"
                 class=" no-border  pr-2"
                 style="float:left;"
                 :style="'width:' + itemWidth + 'px;'">
                <KanbanStatus
                    :kanban_id="kanban.id"
                    :editable="editable"
                    :newStatus=true
                    v-on:status-added="handleStatusAddedWithoutWebsocket"
                    />
            </div>
        </draggable>
        <!-- ./Columns -->
        <div v-if="pusher == 1"
             class="card p-2"
             style="position: fixed;right: 15px;bottom: 30px;">

            <span class="pull-left">
                <i class="fa fa-user"></i> {{ this.usersOnline.length }}
            </span>
        </div>
    </div>
</template>

<script>
import draggable from "vuedraggable";
import KanbanItem from './KanbanItem';
import KanbanItemCreate from "./KanbanItemCreate";
import KanbanStatus from "./KanbanStatus";

export default {
        props: {
            'kanban': Object,
            'editable': true,
            'pusher': false,
            'search': ''
        },
        watch: {
            statuses:{
                deep: true,
                handler(){
                    //console.log('The list of colours has changed!');
                }
            }
        },
        data() {
            return {
                statuses: [],
                newItem: 0, // track the ID of the status we want to add to
                newStatus: 0,
                itemWidth: 320,
                item: null,
                autoRefresh: false,
                refreshRate: 5000,
                usersOnline:[]
            };
        },
        methods: {
            sync(){
                axios.get("/kanbanStatuses/" + this.kanban.id + "/checkSync")
                    .then(res => {
                        if (res.data.message !== 'uptodate'){
                            this.refreshRate = 5000;
                            this.statuses = res.data.message.statuses;
                        } else {
                            this.refreshRate +=1000; //slow down refreshing, if nothing happens
                            //console.log(res.data.message +', set refresh to ' + this.refreshRate * 0.001 + ' seconds');
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
                        if (this.pusher == 0){
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
            handleStatusAddedWithoutWebsocket(newStatus){
                if (this.pusher == 0){
                    this.handleStatusAdded(newStatus);
                }
            },
            handleStatusAdded(newStatus){
                newStatus['items'] = [];            //add items to prevent error if item is created without reloading page
                this.statuses.push(newStatus);
                this.closeForm();
            },
            handleStatusUpdatedWithoutWebsocket(newStatus){
                if (this.pusher == 0){
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
                if (this.pusher == 0){
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
                if (this.pusher == 0){
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
                if (this.pusher == 0){
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
                if (this.pusher == 0){
                    this.handleItemUpdated(updatedItem);
                }
            },
            handleItemUpdated(updatedItem){
                //console.log(updatedItem);
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


            startPusher(){
                if (this.pusher == 1){
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
        },
        mounted() {
            // Listen for the 'Kanban' event in the 'Presence.App.Kanban' presence channel
            this.startPusher();
        },
        created () {
            this.statuses = this.kanban.statuses;

            if (this.kanban.auto_refresh === 1){
                this.autoRefresh = true;
                this.timer();
            } else {
                this.autoRefresh = false;
            }
        },
        computed: {
            columnDragOptions() {
                return {
                  animation: 200,
                  group: "column-list",
                  dragClass: "status-drag"
                };
            },
            itemDragOptions() {
                return {
                  animation: 200,
                  group: "item-list",
                  dragClass: "status-drag"
                };
            },
            kanbanWidth() {
                return "width: "+ ((this.statuses.length) * this.itemWidth +this.itemWidth) +"px;";
            },
        },
        components: {
            KanbanStatus,
            draggable,
            KanbanItem,
            KanbanItemCreate,
        }
    }
</script>
<style scoped>
.status-drag {
    transition: transform 0.5s;
    transition-property: all;
}
</style>
