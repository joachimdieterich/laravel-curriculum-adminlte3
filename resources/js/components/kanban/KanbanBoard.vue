<template>
    <div :style="kanbanWidth"
        class="m-0">
        <!-- Columns (Statuses) -->
        <draggable
            v-model="statuses"
            v-bind="columnDragOptions"
            :options="{disabled: !editable}"
            @end="handleStatusMoved">
            <div
                v-for="(status, index) in statuses"
                :key="'header_'+status.id"
                class=" no-border pr-3"
                :style="'float:left; width:' + itemWidth + 'px;'">
                 <KanbanStatus
                     :kanban_id="status.kanban_id"
                     :status="status"
                     :editable="editable"
                     v-on:status-destroyed="handleStatusDestroyed"/>
                <div style="margin-top:15px; bottom:0;overflow-y:scroll; z-index: 1"
                     :style="'width:' + itemWidth + 'px;'"
                      class="hide-scrollbars">
                    <draggable
                        class="flex-1 overflow-hidden hide-scrollbars"
                        v-model="status.items"
                        v-bind="itemDragOptions"
                        @end="handleItemMoved"
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
                                     v-on:item-destroyed="handleItemDestroyed"
                                     v-on:item-updated="handleItemUpdated"
                                     v-on:item-edit="handleItemEdit"/>
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
                        v-on:item-added="handleItemAdded"
                        v-on:item-updated="handleItemUpdated"
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
                    v-on:status-added="handleStatusAdded"/>
            </div>
        </draggable>
        <!-- ./Columns -->
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
            handleStatusMoved() {
                this.sendChange("/kanbanStatuses/sync");
             },
            handleItemMoved() {
                this.sendChange("/kanbanItems/sync")
            },
            sendChange(url) {
                axios.put(url, {columns: this.statuses})
                    .then(res => { // Tell the parent component we've added a new task and include it
                        this.statuses = res.data.message.statuses;
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
            handleStatusAdded(newStatus){
                newStatus['items'] = [];            //add items to prevent error if item is created without reloading page
                this.statuses.push(newStatus);
                this.closeForm();
            },
            handleItemAdded(newItem) {      // add an item to the correct column in our list
                const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should replace the item
                    status => status.id === newItem.kanban_status_id
                );
                this.statuses[statusIndex].items.push(newItem);       // Add newly created item to our column

                this.closeForm();                                     // Reset and close the AddItemForm
            },
            handleItemDestroyed(item){
                const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should add the item
                    status => status.id === item.kanban_status_id
                );

                let index = this.statuses[statusIndex].items.indexOf(item);

                this.statuses[statusIndex].items.splice(index, 1);
            },
            handleItemEdit(item){
                this.newItem = item.kanban_status_id;
                this.item = item;
            },
            handleItemUpdated(updatedItem){
                const statusIndex = this.statuses.findIndex(            // Find the index of the status where we should replace the item
                    status => status.id === updatedItem.kanban_status_id
                );
                const itemIndex = this.statuses[statusIndex].items.findIndex(   // Find the index of the item where we should replace the item
                    item => item.id === updatedItem.id
                );

                this.statuses[statusIndex].items[itemIndex] = updatedItem;       // Add updated item to our column
                this.closeForm();                                     // Reset and close the AddItemForm
                this.item =  null; //re
            },
            handleStatusDestroyed(status){
                let index = this.statuses.indexOf(status);
                this.statuses.splice(index, 1);
            },
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
