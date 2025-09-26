<template>
    <div
        id="kanban-container"
        class="kanban-container w-print-auto"
    >
        <img v-if="kanban.medium_id"
            class="position-absolute p-0 h-100 w-100"
            style="object-fit: cover;"
            :src="'/media/' + kanban.medium_id + '?preview=true'"
            alt="background image"
        />
        <div
            class="d-print-none position-absolute pointer"
            style="top: 10px; left: 10px; line-height: 1; z-index: 10;"
            :style="{ color: textColor }"
            @click="toggleFullscreen"
        >
            <i class="fa fa-expand"></i>
        </div>
        <div
            class="d-print-none position-absolute pointer"
            :class="kanban.collapse_items && 'collapsed'"
            style="top: 10px; right: 10px; line-height: 1; z-index: 10;"
            :style="{ color: textColor }"
            @click="toggleCollapseAll"
        >
            <i class="fa fa-angle-up"></i>
        </div>

        <div
            id="kanban-wrapper"
            class="kanban-wrapper position-relative"
            :style="'background-color: ' + kanban.color + 'B2;'"
        >
            <!-- Columns (Statuses) -->
            <draggable
                v-model="kanban.statuses"
                v-bind="columnDragOptions"
                item-key="id"
                handle=".handle"
                class="d-flex m-0 h-100"
                style="width: max-content; gap: 16px;"
                :move="isLocked"
                @end="syncStatusMoved"
            >
                <template #item="{ element: status, index }">
                    <span v-if="status.visibility || $userId == kanban.owner_id || $userId == status.owner_id"
                        :id="'status-' + status.id"
                        :key="'drag_status_' + status.id"
                        class="d-flex flex-column h-100"
                        :style="{
                            width:  itemWidth + 'px',
                            opacity: !status.visibility ? '0.7' : '1'
                        }"
                        tabindex="-1"
                    >
                        <KanbanStatus
                            :status="status"
                            :editable="editable"
                            :allow_copy="kanban.allow_copy"
                            :kanban_owner_id="kanban.owner_id"
                            :only_edit_owned_items="kanban.only_edit_owned_items"
                            :key="status.id"
                            :websocket="websocket && kanban.auto_refresh"
                            filter=".ignore"
                        />
                        <div v-if="editable"
                            :id="'kanbanItemCreateButton_' + index"
                            class="btn btn-flat p-1 my-1 mx-auto"
                            @click="openItemModal(status.id)"
                        >
                            <i class="d-print-none text-white fa fa-2x fa-plus-circle"></i>
                        </div>
                        <div v-else class="py-2"></div>
                        <draggable
                            v-model="status.items"
                            v-bind="itemDragOptions"
                            item-key="id"
                            handle=".handle"
                            :data-status-id="status.id"
                            class="kanban-items-container d-flex flex-column hide-scrollbars"
                            style="overflow-y: scroll;"
                            :move="isLocked"
                            @end="syncItemMoved"
                        >
                            <template #item="{ element: item }">
                                <span :key="'drag_item_' + item.id">
                                    <KanbanItem v-if="(item.visibility && visibleFromTo(item.visible_from, item.visible_until))
                                            || ($userId == item.owner_id)
                                            || ($userId == kanban.owner_id)"
                                        :key="item.id"
                                        :editable="editable"
                                        :commentable="kanban.commentable"
                                        :only_edit_owned_items="kanban.only_edit_owned_items"
                                        :collapse_items="kanban.collapse_items"
                                        :allow_copy="kanban.allow_copy"
                                        :ref="'kanbanItemId' + item.id"
                                        :index="status.id + '_' + item.id"
                                        :item="item"
                                        :width="itemWidth"
                                        :kanban_owner_id="kanban.owner_id"
                                        :websocket="websocket && kanban.auto_refresh"
                                        filter=".ignore"
                                    />
                                </span>
                            </template>
                        </draggable>
                    </span>
                </template>
                <template #footer>
                    <div v-if="editable"
                        class="d-print-none no-border float-left pr-2"
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
            <KanbanStatusModal :kanban="initialKanban"/>
            <MediumModal/>
            <MediumPreviewModal/>
            <SubscribeModal/>
            <ConfirmModal
                :showConfirm="show_item_copy"
                :title="trans('global.kanbanItem.copy')"
                :description="trans('global.kanbanItem.copy_helper')"
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
            <small v-text="kanban.title"></small>
            <button v-if="kanban.owner_id == $userId || checkPermission('is_admin')"
                type="button"
                class="btn text-secondary px-2 mx-1"
                @click="editKanban(kanban)"
            >
                <i class="fa fa-pencil-alt"></i>
            </button>

            <button v-if="kanban.owner_id == $userId || checkPermission('is_admin')"
                type="button"
                class="btn text-secondary px-2 mx-1"
                @click="share()"
            >
                <i class="fa fa-share-alt"></i>
            </button>

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

            <p class="h6">{{ trans('global.owner') }}: {{ initialKanban.owner.firstname + ' ' + initialKanban.owner.lastname }}</p>
        </Teleport>
        <Teleport to="#contributors">
            <contributors-list v-if="Object.values(currentContributors).length > 1" :contributors="currentContributors" :heading="true"></contributors-list>
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
import MediumPreviewModal from "../media/MediumPreviewModal.vue";
import SubscribeModal from "../subscription/SubscribeModal.vue";
import KanbanModal from "../kanban/KanbanModal.vue";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";
import ContributorsList from "../uiElements/ContributorsList.vue";
import {useToast} from "vue-toastification";

export default {
    props: {
        initialKanban: {
            type: Object,
            default: null,
        },
        editable: {
            type: Boolean,
            default: true,
        },
        websocket: {
            type: Boolean,
            default: false,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
        }
    },
    data() {
        return {
            kanban: {},
            currentContributors: {},
            newItem: 0, // track the ID of the status we want to add to
            newStatus: 0,
            itemWidth: 320,
            item: null,
            copy_id: null,
            show_item_copy: false,
            show_status_copy: false,
            refreshRate: 5000,
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
            }
        },
        toggleCollapseAll(e) {
            const collapse = e.target.parentElement.classList.toggle('collapsed') ? 'hide' : 'show';
            $('#kanban-wrapper .card-body').collapse(collapse);
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
        visibleFromTo(visible_from, visible_until) {
            if (visible_from == null && visible_until == null) return true; // no restrictions

            const now = new Date();
            const from = new Date(visible_from); // null => 1970-01-01T00:00:00.000Z
            const until = new Date(visible_until);

            return now > from && now < until;
        },
        syncStatusMoved(e) {
            if (e.oldIndex === e.newIndex) return; // no change

            const statusChanges = [];
            const start = e.oldIndex < e.newIndex ? e.oldIndex : e.newIndex;
            const end = e.oldIndex < e.newIndex ? e.newIndex : e.oldIndex;

            for (let i = start; i <= end; i++) {
                const status = this.kanban.statuses[i];
                status.order_id = i;
                statusChanges.push({
                    id: status.id,
                    order_id: status.order_id,
                });
            }

            this.sendChange("/kanbanStatuses/sync", statusChanges);
        },
        syncItemMoved(e) {
            if (e.from === e.to && e.oldIndex === e.newIndex) return; // no change

            // get status from 'moved to'-position (data-attribtue)
            const status = this.kanban.statuses.find(status => status.id == e.to.dataset.statusId);
            // find moved item based on newIndex
            const movedItem = status.items[e.newIndex];
            movedItem.order_id = e.newIndex;
            movedItem.kanban_status_id = status.id;

            let itemChanges = [{
                id: movedItem.id,
                order_id: movedItem.order_id,
                kanban_status_id: status.id,
            }];
            // if the item was moved within the same status, we only need to update the order_ids of its items
            if (e.from === e.to) {
                // define start/end index without the moved item
                const start = e.oldIndex < e.newIndex ? e.oldIndex : e.newIndex + 1;
                const end = e.oldIndex < e.newIndex ? e.newIndex - 1 : e.oldIndex;
                // update order_id for all items in the range
                for (let i = start; i <= end; i++) {
                    const item = status.items[i];
                    item.order_id = i;
                    // save the new order_id so it can be updated on the server
                    itemChanges.push({ id: item.id, order_id: item.order_id, kanban_status_id: status.id });
                }
            } else {
                // on 'moved to'-status, increase order_id of all items after the moved item
                for (let i = e.newIndex + 1; i < status.items.length; i++) {
                    const item = status.items[i];
                    item.order_id = i;
                    itemChanges.push({ id: item.id, order_id: item.order_id, kanban_status_id: status.id });
                }

                // on 'moved from'-status, decrease order_id of all items after the original position
                const oldStatus = this.kanban.statuses.find(status => status.id == e.from.dataset.statusId);
                for (let i = e.oldIndex; i < oldStatus.items.length; i++) {
                    const item = oldStatus.items[i];
                    item.order_id = i;
                    itemChanges.push({ id: item.id, order_id: item.order_id, kanban_status_id: oldStatus.id });
                }
            }

            this.sendChange("/kanbanItems/sync", itemChanges);
        },
        sendChange(url, changes) {
            const data = url == '/kanbanItems/sync' ? { items: changes } : { statuses: changes };

            axios.put(url, data)
                .catch(err => {
                    console.log(err);
                });
        },
        openItemModal(status_id) {
            this.globalStore?.showModal('kanban-item-modal', {
                item: {
                    kanban_id: this.kanban.id,
                    kanban_status_id: status_id,
                    color: this.kanban.statuses.find(s => s.id === status_id).color,
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
                .then(response => this.$eventHub.emit('kanban-item-added-' + response.data.kanban_status_id, response.data));
        },
        handleStatusAdded(newStatus) {
            // if the status already exists do nothing
            if (this.kanban.statuses.filter(s => s.id === newStatus.id).length !== 0) {
                return;
            }

            // add items to prevent error if item is created without reloading page
            if (newStatus['items'] == undefined) newStatus['items'] = [];
            this.kanban.statuses.push(newStatus);
        },
        handleStatusUpdated(newStatus) {
            let status = this.kanban.statuses.find(s => s.id === newStatus.id);

            Object.assign(status, newStatus);

            this.handleStatusMoved();
        },
        handleStatusDeleted(status) {
            let index = this.kanban.statuses.findIndex(s => s.id === status.id);

            this.kanban.statuses.splice(index, 1);
        },
        handleStatusMoved() {
            let newStatusesOrderTemp = [];

            this.kanban.statuses.forEach((status) => {
                newStatusesOrderTemp.splice(status.order_id, 0, status);
            });

            this.kanban.statuses = newStatusesOrderTemp;
        },
        startWebsocket() {
            if (this.websocket === true && this.kanban.auto_refresh === true) {
                this.$echo
                    .join('App.Kanban.' + this.kanban.id)
                    .here((users) => {
                        for(let user of users) {
                            this.currentContributors[user.id] = user;
                        }
                    })
                    .listen('.KanbanUpdated', (payload) => {
                        this.$eventHub.emit('kanban-updated', payload.model);
                    })
                    .joining((user) => {
                        this.currentContributors[user.id] = user;
                        this.toast.info(this.trans('global.kanban.contributor_joined') + ': ' + user.firstname + ' ' + user.lastname);
                    })
                    .leaving((user) => {
                        delete this.currentContributors[user.id];
                        this.toast.info(this.trans('global.kanban.contributor_left') + ': ' + user.firstname + ' ' + user.lastname);
                    });
            }
        },
        stopWebsocket() {
            if (this.websocket === true && this.kanban.auto_refresh === true) {
                this.$echo.leave('App.Kanban.' + this.kanban.id);
            }
        },
        isLocked(value) {
            return !(value.draggedContext.element.locked == true && this.$userId != value.draggedContext.element.owner_id);
        },
    },
    mounted() {
        this.kanban = this.initialKanban;

        this.startWebsocket();

        // KANBAN Events
        this.$eventHub.on('kanban-updated', (updatedKanban) => {
            this.kanban = updatedKanban;
        });

        // STATUS Events
        this.$eventHub.on('kanban-status-created', (status) => {
            this.handleStatusAdded(status);
        });
        this.$eventHub.on('kanban-status-updated', (status) => {
            this.handleStatusUpdated(status);
        });
        this.$eventHub.on('kanban-status-deleted', (status) => {
            this.handleStatusDeleted(status);
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
    unmounted() {
        this.stopWebsocket();
    },
    computed: {
        textColor: function() {
            if (this.kanban.color == "" || this.kanban.color == null) return;
            return this.$textcolor(this.kanban.color, '#333333');
        },
        columnDragOptions() {
            return {
                animation: 200,
                delay: 200,
                delayOnTouchOnly: true,
                group: "columns",
                disabled: !this.editable,
            };
        },
        itemDragOptions() {
            return {
                animation: 200,
                delay: 200,
                delayOnTouchOnly: true,
                group: "items",
                disabled: !this.editable,
            };
        },
    },
    components: {
        ContributorsList,
        KanbanStatus,
        draggable,
        KanbanItem,
        KanbanItemModal,
        MediumModal,
        MediumPreviewModal,
        SubscribeModal,
        KanbanModal,
        KanbanStatusModal,
        ConfirmModal,
    },
}
</script>
<style scoped>
.kanban-container {
    background-color: #fff;
    position: relative;
    width: 100%;
}
.kanban-wrapper {
    height: 100%;
    width: 100%;
    padding: 2rem;
    overflow-x: auto;
    overflow-y: clip;
}
.kanban-items-container { scroll-behavior: smooth; }
.kanban-items-container > :last-child > .card { margin-bottom: 0; }
@media (max-width: 991px) {
    .kanban-container {
        width: 100vw;
        margin-left: -1rem;
    }
}
div[id^="item"], span[id^="status"] {
    transition: opacity 0.25s linear;
    &:hover, &:focus { opacity: 1 !important; }
}
</style>
