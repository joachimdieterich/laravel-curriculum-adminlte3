<template>
    <div class="card-header border-bottom-0 p-0 kanban-header">
        <div v-if="newStatus === true"
            id="kanbanStatusCreate"
        >
            <strong
                class="text-secondary btn px-1 py-0"
                @click="edit()"
            >
                <i class="fa fa-plus"></i> {{ trans('global.kanbanStatus.create') }}
            </strong>
        </div>
        
        <div v-else>
            <strong>{{ status.title }}</strong>
            <div v-if="($userId == kanban.owner_id)
                    || (editable && $userId == status.owner_id)
                    || (editable && status.editable && !kanban.only_edit_owned_items)"
                :id="'kanbanStatusDropdown_' + status.id"
                class="btn btn-flat py-0 pl-0 pull-left"
                data-toggle="dropdown"
                aria-expanded="false"
            >
                <i class="text-muted fas fa-bars"></i>
                <div
                    class="dropdown-menu"
                    x-placement="top-start"
                >
                    <div>
                        <button
                            name="kanbanStatusEdit"
                            class="dropdown-item py-1"
                            @click="edit()"
                        >
                            <i class="fa fa-pencil-alt mr-4"></i>
                            {{ trans('global.kanbanStatus.edit') }}
                        </button>
                        <div v-if="($userId == status.owner_id) || (editable && $userId == kanban.owner_id)">
                            <hr class="my-1">
                            <button
                                v-permission="'kanban_delete'"
                                name="kanbanStatusDelete"
                                class="dropdown-item py-1 text-red"
                                @click="confirmItemDelete()"
                            >
                                <i class="fa fa-trash mr-4"></i>
                                {{ trans('global.kanbanStatus.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="$userId == kanban.owner_id
                    || (!status.locked || $userId == status.owner_id)"
                class="pull-right handle pointer"
            >
                <span class="fa-stack fa-1x">
                    <i v-if="editable"
                        class="fa-solid fa-arrows-up-down-left-right fa-stack-1x"
                    ></i>
                    <i v-if="status.locked"
                        class="fa-solid fa-lock fa-stack-1x text-muted"
                        style="left: 10px; top: 10px;"
                    ></i>
                </span>
            </div>
        </div>

        <Teleport to="body">
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.kanbanStatus.delete')"
                :description="trans('global.kanbanStatus.delete_helper')"
                @close="this.showConfirm = false"
                @confirm="() => {
                    this.showConfirm = false;
                    this.delete();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>
<script>
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'KanbanStatus',
    props: {
        kanban: {},
        status: {
            type: Object,
        },
        editable: true,
        newStatus: false,
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
            event: ''
        }
    },
    methods: {
        edit() {
            this.globalStore?.showModal('kanban-status-modal', {
                status: this.status ?? {},
                method: this.method,
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
        if (this.newStatus) this.method = 'post';
    },
    components: {
        ConfirmModal,
    }
}
</script>
