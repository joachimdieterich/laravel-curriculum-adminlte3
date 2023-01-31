<template>
    <div class="card-header border-bottom-0 p-0 kanban-header"
         :key="form.id">
        <span v-if="editor !== false">
            <input
                :id="'title_'+ form.id"
                ref="newStatus"
                type="text"
                v-model="form.title"
                class="w-100"
                style="font-size: 1.1rem; font-weight: 400; border: 0; border-bottom: 1px; border-style:solid; margin: 0;"
            />
             <button :name="'kanbanStatusSave_'+form.id"
                     class="btn btn-primary p-2 m-2"
                     @click="submit()">
                {{ trans('global.save') }}
            </button>
        </span>
        <span v-else-if="newStatus === true"
              :id="'kanbanStatusCreate_'+form.id">
            <strong
                class="text-secondary btn px-1 py-0"
                @click="edit()">
                <i class="fa fa-plus"></i> {{ trans('global.kanbanStatus.create') }}
            </strong>
        </span>
        <span v-else>
            <strong>{{ form.title }}</strong>
            <div v-if="editable"
                 :id="'kanbanStatusDropdown_'+form.id"
                 class="btn btn-flat py-0 pl-0 pull-left"
                 data-toggle="dropdown"
                 aria-expanded="false">
                <i class="text-muted fas fa-bars"></i>
                <div class="dropdown-menu"
                     x-placement="top-start">
                    <span>
                        <button
                            name="kanbanStatusEdit"
                            class="dropdown-item py-1"
                            @click="edit()">
                            <i class="fa fa-pencil-alt mr-4"></i>
                            {{ trans('global.kanbanStatus.edit') }}
                        </button>
                        <hr class="my-1">
                        <button
                            v-can="'kanban_delete'"
                            name="kanbanStatusDelete"
                            class="dropdown-item py-1 text-red "
                            @click="confirmStatusDelete()">
                            <i class="fa fa-trash mr-4"></i>
                            {{ trans('global.delete') }}
                        </button>
                    </span>
                </div>
            </div>
        </span>

        <Modal
            :id="'statusModal_'+form.id"
            css="danger"
            :title="trans('global.kanbanStatus.delete')"
            :text="trans('global.kanbanStatus.delete_helper')"
            :ok_label="trans('global.kanbanStatus.delete')"
            v-on:ok="deleteStatus"
        />
    </div>
</template>
<script>
import Form from "form-backend-validation";
import Modal from "./../uiElements/Modal";

export default {
    name: 'KanbanStatus',
    props: {
        kanban_id: {
            type: Number,
            default: 0
        },
        status: {},
        'editable': true,
        'newStatus': false,
    },
    data() {
      return {
          editor: false,
          form: new Form({
              'id': 0,
              'title': '',
              'kanban_id': '',
          }),
          url: '',
          method: 'patch',
          event: ''
      }
    },
    methods: {
        edit() {
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
                this.form.kanban_id = this.kanban_id;
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
        confirmStatusDelete(){
            $('#statusModal_'+ this.form.id).modal('show');
        },
        deleteStatus(){
            axios.delete("/kanbanStatuses/"+this.status.id)
                .then(res => {
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
    components: {
        Modal,
    }
}
</script>
