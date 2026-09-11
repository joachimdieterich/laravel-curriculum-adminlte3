<template>
    <div
        class="kanban-status mx-2"
        :class="status === null && 'p-0'"
        :style="{ backgroundColor: status?.color }"
    >
        <button v-if="status === null"
            id="kanbanStatusCreate"
            class="btn btn-default d-flex align-items-center w-100 p-3 border-0 rounded-3"
            type="button"
            @click="openModal()"
        >
            <i class="fa fa-plus me-1"></i>
            <span>{{ trans('global.kanbanStatus.create') }}</span>
        </button>
        <div v-else
            v-show="showWithSearch"
            class="d-flex align-items-center justify-content-between"
            :style="'color: ' + textColor"
        >
            <strong>{{ status.title }}</strong>
            <div class="d-flex gap-1">
                <button v-if="$userId == kanban_owner_id
                        || (!status.locked || $userId == status.owner_id)"
                    class="btn position-relative handle d-print-none"
                    :class="textColor === '#000' ? 'btn-icon' : 'btn-icon-alt'"
                    type="button"
                    tabindex="-1"
                >
                    <i v-if="editable"
                        class="fa fa-arrows-up-down-left-right"
                    ></i>
                    <i v-if="status.locked"
                        class="fa fa-lock text-muted position-absolute"
                        style="right: 2px; bottom: 2px; cursor: not-allowed;"
                    ></i>
                </button>
                <div v-if="edit_rights || copy_rights || delete_rights"
                    class="dropdown d-print-none"
                >
                    <button
                        class="btn"
                        :class="textColor === '#000' ? 'btn-icon' : 'btn-icon-alt'"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        :title="trans('global.kanbanStatus.dropdown')"
                        :aria-label="trans('global.kanbanStatus.dropdown')"
                    >
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <button v-if="edit_rights"
                            type="button"
                            class="dropdown-item"
                            @click="openModal()"
                        >
                            <i class="fa fa-pencil-alt"></i>
                            {{ trans('global.kanbanStatus.edit') }}
                        </button>
                        <button v-if="copy_rights"
                            type="button"
                            class="dropdown-item"
                            @click="confirmCopy()"
                        >
                            <i class="fa fa-copy"></i>
                            {{ trans('global.kanbanStatus.copy') }}
                        </button>

                        <hr v-if="delete_rights" class="my-1">

                        <button v-if="delete_rights"
                            v-permission="'kanban_delete'"
                            type="button"
                            class="dropdown-item text-danger"
                            @click="confirmDeletion()"
                        >
                            <i class="fa fa-trash"></i>
                            {{ trans('global.kanbanStatus.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'KanbanStatus',
    emits: [
        'kanban-show-copy',
        'kanban-show-delete',
        'kanban-status-updated',
        'kanban-status-delete',
        'show-with-search'
    ],
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
        websocket: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            url: '',
            method: 'patch',
            edit_rights: false,
            copy_rights: false,
            delete_rights: false,
            searchFilter: '',
            forceShow: {},
        }
    },
    methods: {
        openModal() {
            this.globalStore?.showModal('kanban-status-modal', {
                status: this.status ?? {},
                method: this.status === null ? 'post' : 'patch',
            });
        },
        confirmCopy() {
            this.$eventHub.emit('kanban-show-copy', {
                id: this.status.id,
                type: 'status',
            });
        },
        confirmDeletion() {
            this.$eventHub.emit('kanban-show-delete', {
                id: this.status.id,
                type: 'status',
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
        handleItemDeleted(id) {
            // Find the index of the status where we should delete the item
            const itemIndex = this.status.items.findIndex(
                item => item.id === id
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
                        this.$eventHub.emit('kanban-status-deleted', payload.model.id);
                    })
                ;
            }
        },
        stopWebsocket() {
            if (this.websocket === true) {
                this.$echo.leave('App.KanbanStatus.' + this.status.id);
            }
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
    computed: {
        textColor: function() {
            if (this.status === null || !this.status.color) return '#000';
            return this.$textcolor(this.status.color);
        },
        showWithSearch: function () {
            if (typeof this.searchFilter !== 'string') {
                return true;
            }

            let show = this.status.title.toLowerCase().includes(this.searchFilter.toLowerCase()) || this.forcedToShow()

            this.$emit('show-with-search', {
                id: this.status.id,
                show: show
            });

            return show;
        },
    },
    mounted() {
        this.startWebsocket();

        this.$eventHub.on('filter', (filter) => {
            this.searchFilter = filter.searchString.toLowerCase();
        });

        if (this.status !== null) {
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

            // ITEM Events
            this.$eventHub.on('kanban-status-force-show-' + this.status.id, (forceShow) => {
                this.forceShow[forceShow.kanbanItemId] = forceShow.show;
            });
            this.$eventHub.on('kanban-item-added-' + this.status.id, (item) => {
                this.handleItemAdded(item);
            });
            this.$eventHub.on('kanban-item-updated-' + this.status.id, (item) => {
                this.handleItemUpdated(item);
            });
            this.$eventHub.on('kanban-item-deleted-' + this.status.id, (id) => {
                this.handleItemDeleted(id);
            });
        }
    },
    unmounted() {
        this.stopWebsocket();
    },
}
</script>