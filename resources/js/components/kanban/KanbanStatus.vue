<template>
    <div class="card-header border-bottom-0 p-0 kanban-header"
         :key="form.id">
        <div v-if="(editor !== false && form.visibility !== 0 ) || (editor !== false && $userId == status_owner_id ) || (editor !== false && $userId == kanban.owner_id ) "
              filter=".ignore">
            <input
                :id="'title_'+ form.id"
                ref="newStatus"
                type="text"
                v-model="form.title"
                class="w-100"
                style="font-size: 1.1rem; font-weight: 400; border: 0; border-bottom: 1px; border-style:solid; margin: 0;"
            />
             <div class="form-group "
                  v-if="($userId == status_owner_id) || ($userId == kanban.owner_id) ">
                 <span class="custom-control custom-switch custom-switch-on-green">
                    <input  v-model="form.editable"
                            type="checkbox"
                            class="custom-control-input pt-1 "
                            :id="'editable_'+ form.id">
                    <label class="custom-control-label  font-weight-light"
                           :for="'editable_'+ form.id" >
                        {{ trans('global.editable') }}
                    </label>
                </span>
                 <span class="custom-control custom-switch custom-switch-on-green">
                    <input  v-model="form.locked"
                            type="checkbox"
                            class="custom-control-input pt-1 "
                            :id="'locked_'+ form.id">
                    <label class="custom-control-label  font-weight-light"
                           :for="'locked_'+ form.id" >
                        {{ trans('global.locked') }}
                    </label>
                </span>
                 <span class="custom-control custom-switch custom-switch-on-green">
                    <input
                        v-model="form.visibility"
                            type="checkbox"
                            class="custom-control-input pt-1 "
                            :id="'visibility_'+ form.id">
                    <label class="custom-control-label font-weight-light"
                           :for="'visibility_'+ form.id" >
                        {{ trans('global.visibility') }}
                    </label>
                </span>
            </div>

             <button :name="'kanbanStatusSave_'+form.id"
                     class="btn btn-primary p-2 pull-right"
                     @click="submit()">
                {{ trans('global.save') }}
            </button>
        </div>
        <div v-else-if="newStatus == true"
              :id="'kanbanStatusCreate_'+form.id">
            <strong
                class="text-secondary btn px-1 py-0"
                @click="edit()">
                <i class="fa fa-plus"></i> {{ trans('global.kanbanStatus.create') }}
            </strong>
        </div>
        <div v-else>
            <strong>{{ form.title }}</strong>
            <div v-if="(editable == 1 && status.editable == true && status.visibility == true && kanban.only_edit_owned_items == false)
                || (editable == 1 && $userId == status_owner_id )
                || ($userId == kanban.owner_id )"
                 :id="'kanbanStatusDropdown_'+form.id"
                 class="btn btn-flat py-0 pl-0 pull-left"
                 data-toggle="dropdown"
                 aria-expanded="false">
                <i class="text-muted fas fa-bars"></i>
                <div class="dropdown-menu"
                     x-placement="top-start">
                    <div>
                        <button
                            name="kanbanStatusEdit"
                            class="dropdown-item py-1"
                            @click="edit()">
                            <i class="fa fa-pencil-alt mr-4"></i>
                            {{ trans('global.kanbanStatus.edit') }}
                        </button>
                        <div v-if="($userId == status_owner_id) || (editable == 1 && $userId == kanban.owner_id)">
                            <hr class="my-1">
                            <button
                                v-can="'kanban_delete'"
                                name="kanbanStatusDelete"
                                class="dropdown-item py-1 text-red "
                                @click="confirmItemDelete()">
                                <i class="fa fa-trash mr-4"></i>
                                {{ trans('global.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="(!status.locked || $userId == status.owner_id) || $userId == kanban.owner_id"
                 class="pull-right handle pointer">
                <span class="fa-stack fa-1x">
                  <i class="fa-solid fa-arrows-up-down-left-right fa-stack-1x"></i>
                  <i v-if="status.locked"
                     class="fa-solid fa-lock fa-stack-1x text-muted" style="left:10px;top:10px"></i>
                </span>
            </div>

        </div>

        <Teleport to="body">
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.kanbanStatus.delete')"
                :description="trans('global.kanbanStatus.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
        </Teleport>

    </div>
</template>
<script>
import Form from "form-backend-validation";
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'KanbanStatus',
    props: {
        kanban: {},
        status: {
            type: Object,
        },
        'editable': true,
        'newStatus': false,
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
      return {
          component_id: this.$.uid,
          showConfirm: false,
          currentStatus : {},
          editor: false,
          form: new Form({
              'id': '',
              'title': '',
              'kanban_id': '',
              'locked': false,
              'editable': true,
              'visibility': true,
              'visible_from': null,
              'visible_until': null,
          }),
          url: '',
          method: 'patch',
          event: ''
      }
    },
    methods: {
        edit() {
            this.globalStore?.showModal('kanban-status-modal', {});
            this.editor = true;
            if (this.newStatus === true) {
                this.form.id =  0;
                this.form.title = '';
            }
            this.$nextTick(function () {
                this.$refs['newStatus'].focus();
            });
        },
        submit() {
            if (this.form.id === 0){
                this.url = '/kanbanStatuses';
                this.method = 'post';
                this.form.kanban_id = this.kanban.id;
                this.event = 'status-added';
            } else {
                this.url = '/kanbanStatuses/' + this.form.id;
                this.method = 'patch';
                this.event = 'status-updated';
            }
            axios[this.method](this.url, this.form)
                .then(res => {
                    this.$emit(this.event, res.data.message);
                    this.form = res.data.message; //selfupdate
                })
                .catch(error => { // Handle the error returned from our request
                    console.log(error);
                });
            this.editor = false;
        },
        confirmItemDelete(status){
            this.currentStatus = status;
            this.showConfirm = true;
        },
        destroy(){
            axios.delete("/kanbanStatuses/"+this.form.id)
                .then(() => {
                    this.$emit("status-destroyed", this.status);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
    mounted() {
        if (this.newStatus === true) {
            this.form.id = 0;
            this.form.title = window.trans.global.kanbanStatus.create;
        } else {
            this.form = this.status;
        }
    },
    computed: {
        status_owner_id: function () {
            if (typeof(this.status) == 'undefined') {
                return -1;
            } else {
                return this.status.owner_id
            }
        },
    },
    components: {
        ConfirmModal
    }
}
</script>
