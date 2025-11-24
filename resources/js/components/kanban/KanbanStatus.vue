<template>
    <div
        class="kanban-header"
        v-show="newStatus || showWithSearch(status.title)"
        :style="{ backgroundColor: status?.color }"
    >
        <div v-if="newStatus"
            id="kanbanStatusCreate"
            class="d-flex align-items-center pointer"
            @click="edit()"
        >
            <span class="text-secondary btn px-1 py-0">
                <i class="fa fa-plus"></i>
                {{ trans('global.kanbanStatus.create') }}
            </span>
        </div>
        <div v-else
             v-show="showWithSearch(status.title)"
            class="d-flex align-items-center"
            :style="'color:' + $textcolor(status.color)"
        >
            <div v-if="edit_rights || copy_rights || delete_rights"
                :id="'kanbanStatusDropdown_' + status.id"
                class="d-print-none btn btn-flat py-0 pl-0 pull-left"
                data-toggle="dropdown"
                aria-expanded="false"
            >
                <i
                    class="fas fa-bars"
                    :style="'color:' + $textcolor(status.color)"
                ></i>
                <div
                    class="dropdown-menu"
                    x-placement="top-start"
                >
                    <div>
                        <div v-if="edit_rights">
                            <button
                                name="kanbanStatusEdit"
                                class="dropdown-item text-secondary py-1"
                                @click="edit()"
                            >
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.kanbanStatus.edit') }}
                            </button>
                        </div>

                        <div v-if="copy_rights">
                            <button
                                name="kanbanStatusCopy"
                                class="dropdown-item text-secondary py-1"
                                @click="confirmCopy()"
                            >
                                <i class="fa fa-copy mr-2"></i>
                                {{ trans('global.kanbanStatus.copy') }}
                            </button>
                        </div>

                        <div v-if="delete_rights">
                            <hr class="my-1">
                            <button
                                v-permission="'kanban_delete'"
                                name="kanbanStatusDelete"
                                class="dropdown-item py-1 text-red"
                                @click="confirmItemDelete()"
                            >
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.kanbanStatus.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <strong>{{ status.title }}</strong>
            <div v-if="$userId == kanban_owner_id
                    || (!status.locked || $userId == status.owner_id)"
                class="handle d-print-none ml-auto pointer"
            >
                <span class="position-relative">
                    <i v-if="editable"
                        class="fa fa-arrows-up-down-left-right"
                    ></i>
                    <i v-if="status.locked"
                        class="fa fa-lock text-muted position-absolute"
                        style="left: 8px; top: 10px; cursor: not-allowed;"
                    ></i>
                </span>
            </div>
        </div>

        <Teleport to=".content">
            <ConfirmModal
                :showConfirm="showConfirm"
                :title="trans('global.kanbanStatus.delete')"
                :description="trans('global.kanbanStatus.delete_helper')"
                @close="this.showConfirm = false"
                @confirm="() => {
                    this.showConfirm = false;
                    this.delete();
                }"
            />
        </Teleport>
    </div>
</template>
<script>
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'KanbanStatus',
    props: {
        status: {
            type: Object,
            default: null,
        },
        kanban_owner_id: {
            type: Number,
            default: null,
        },
        editable: {
            type: Boolean,
            default: false,
        },
        allow_copy: {
            type: Boolean,
            default: false,
        },
        only_edit_owned_items: {
            type: Boolean,
            default: true,
        },
        newStatus: {
            type: Boolean,
            default: false,
        },
        websocket: {
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
            component_id: this.$.uid,
            showConfirm: false,
            url: '',
            method: 'patch',
            event: '',
            edit_rights: false,
            copy_rights: false,
            delete_rights: false,
            searchFilter: '',
            forceShow: {},
        }
    },
    methods: {
        edit() {
            this.globalStore?.showModal('kanban-status-modal', {
                status: this.status ?? {},
                method: this.method,
            });
        },
        confirmCopy() {
            this.$eventHub.emit('kanban-show-copy', {
                id: this.status.id,
                type: 'status',
            });
        },
        confirmItemDelete() {
            this.showConfirm = true;
        },
        delete() {
            axios.delete("/kanbanStatuses/" + this.status.id)
                .then(() => {
                    this.$eventHub.emit("kanban-status-deleted", this.status);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        handleItemAdded(newItem) {
            // Add newly created item to our column
            this.status.items.push(newItem);
        },
        handleItemUpdated(updatedItem) {
            let item = this.status.items.find(s => s.id === updatedItem.id);

            Object.assign(item, updatedItem);

            this.handleItemMoved(this.status.items);
        },
        handleItemDeleted(item) {
            // Find the index of the status where we should delete the item
            const itemIndex = this.status.items.findIndex(
                i => i.id === item.id
            );
            if (itemIndex === -1) return;

            this.status.items.splice(itemIndex, 1);
        },
        // Reorder items after update
        handleItemMoved(newItems) {
            let newItemsOrderTemp = [];

            newItems.forEach((status) => {
                newItemsOrderTemp.splice(status.order_id, 0, status);
            });

            this.status.items = newItemsOrderTemp;
        },
        startWebsocket() {
            if (this.websocket === true) {
                this.$echo
                    .channel('App.KanbanStatus.' + this.status.id)
                    .listen('.KanbanStatusUpdated', (payload) => {
                        this.$eventHub.emit('kanban-status-updated', payload.model);
                        this.$nextTick(() => {
                            this.handleItemMoved(payload.model.items)
                        });
                    })
                    .listen('.KanbanStatusDeleted', (payload) => {
                        this.$eventHub.emit('kanban-status-deleted', payload.model);
                    })
                ;
            }
        },
        stopWebsocket() {
            if (this.websocket === true) {
                this.$echo.leave('App.KanbanStatus.' + this.status.id);
            }
        },
        showWithSearch: function (stringComparedToSearch) {
            if (!this.searchFilter) {
                return true;
            }

            console.log(this.forcedToShow());

            return stringComparedToSearch.toLowerCase().includes(this.searchFilter.toLowerCase()) || this.forcedToShow();
        },
        forcedToShow: function () {
            for (let key in this.forceShow) {
                if (this.forceShow[key] == true) {
                    return true;
                }
            }

            return false;
        }
    },
    mounted() {
        this.startWebsocket();

        this.$eventHub.on('filter', (filter) => {
            this.searchFilter = filter.searchString.toLowerCase();
        });

        if (this.newStatus) {
            this.method = 'post';
        } else {
            this.edit_rights =
                this.$userId == this.kanban_owner_id
                || this.checkPermission('is_admin')
                // (edit = true and status-owner) or (edit = true and status-edit = true and everyone can edit)
                || (this.editable && (this.$userId == this.status.owner_id || (this.status.editable && !this.only_edit_owned_items)));

            this.copy_rights = this.allow_copy && this.editable;

            this.delete_rights =
                this.$userId == this.kanban_owner_id
                || this.checkPermission('is_admin')
                || this.$userId == this.status.owner_id;
        }

        // ITEM Events
        if (this.status !== null) {
            this.$eventHub.on('kanban-status-force-show-' + this.status.id, (forceShow) => {
                this.forceShow[forceShow.kanbanItemId] = forceShow.show;
            });
            this.$eventHub.on('kanban-item-added-' + this.status.id, (item) => {
                this.handleItemAdded(item);
            });
            this.$eventHub.on('kanban-item-updated-' + this.status.id, (item) => {
                this.handleItemUpdated(item);
            });
            this.$eventHub.on('kanban-item-deleted-' + this.status.id, (item) => {
                this.handleItemDeleted(item);
            });
        }
    },
    unmounted() {
        this.stopWebsocket();
    },
    components: {
        ConfirmModal,
    },
}
</script>
<style scoped>
.kanban-header {
    background-color: white;
    padding: 0.75rem;
    border-radius: 0.5rem;
}
</style>
