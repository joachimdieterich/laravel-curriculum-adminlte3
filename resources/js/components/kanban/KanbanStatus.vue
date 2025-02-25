<template>
    <div
        class="kanban-header"
        :style="{ backgroundColor: color }"
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
            class="d-flex align-items-center"
            :style="'color:' + $textcolor(status.color)"
        >
            <div v-if="edit_rights || copy_rights || delete_rights"
                :id="'kanbanStatusDropdown_' + status.id"
                class="btn btn-flat py-0 pl-0 pull-left"
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
                class="handle ml-auto pointer"
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

        <Teleport to="body">
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
            color: this.status?.color ?? '#FFF',
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
    },
    mounted() {
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
    },
    components: {
        ConfirmModal,
    },
}
</script>