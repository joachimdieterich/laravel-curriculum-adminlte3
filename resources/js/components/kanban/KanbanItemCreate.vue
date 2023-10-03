<template>
    <div class="card mr-3 ">
        <div class="card-body">
            <color-picker-input v-model="form.color"></color-picker-input>
            <div class="form-group">
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    v-model.trim="form.title"
                    :placeholder="trans('global.kanbanItem.fields.title')"
                    required
                    />
                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
            </div>
            <div class="form-group">
                 <textarea
                     id="description"
                     name="description"
                     :placeholder="trans('global.kanbanItem.fields.description')"
                     class="form-control description my-editor "
                     v-model.trim="form.description"
                 ></textarea>
                <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
            </div>



            <div class="p-2">
                <b class="pt-2">{{ trans('global.settings')}}</b>
                <hr class="mt-0">
                <div class="form-group ">
                    <date-picker
                        v-if="editor !== false"
                        class="w-100 mb-2"
                        v-model="form.due_date"
                        type="datetime"
                        valueType="YYYY-MM-DD HH:mm:ss"
                        :placeholder="trans('global.kanbanItem.due_date')">
                    </date-picker>

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
                        <input
                            v-model="form.visibility"
                            type="checkbox"
                            class="custom-control-input pt-1 "
                            :id="'visibility_'+ form.id">
                        <label class="custom-control-label font-weight-light"
                               :for="'visibility_'+ form.id" >
                            {{ trans('global.visibility') }}:
                        </label>
                    </span>

                    <date-picker
                        v-if="form.visibility == 1"
                        class="w-100 pt-2"
                        v-model="form.visible_from"
                        type="datetime"
                        valueType="YYYY-MM-DD HH:mm:ss"
                        :placeholder="trans('global.kanbanItem.fields.visible_from')">
                    </date-picker>
                    <date-picker
                        v-if="form.visibility == 1"
                        class="w-100 pt-2"
                        v-model="form.visible_until"
                        type="datetime"
                        valueType="YYYY-MM-DD HH:mm:ss"
                        :placeholder="trans('global.kanbanItem.fields.visible_until')">
                    </date-picker>
                </div>
            </div>
            
            <button
                name="kanbanItemCancel"
                @click="$emit('item-canceled')"
                type="reset"
                class="btn btn-default"
              >
                {{ trans('global.cancel') }}
            </button>
            <button
                name="kanbanItemSave"
                class="btn btn-primary pull-right"
                @click="submit"
              >
                {{ trans('global.save') }}
            </button>
        </div>
    </div>
</template>

<script>
import Form from 'form-backend-validation';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
/*import kanbanTask from "./KanbanTask";*/

export default {

    props: {
        status,
        item: Object,
        width: Number
    },
    data() {
        return {
            method: 'post',
            requestUrl: '/kanbanItems',
            form: new Form({
                'id':'',
                'title':'',
                'description': '',
                'kanban_id': '',
                'kanban_status_id': '',
                'order_id': 0,
                'color': '#F4F4F4',
                'due_date': null,
                'locked': false,
                'editable': true,
                'visibility': true,
                'visible_from': null,
                'visible_until': null,
            }),
        };
    },
    created() {
        if ( this.item !== null ) {
            this.form.color = this.item.color;
        }
    },
    mounted() {
        if ( this.item !== null ) {
            this.form.id = this.item.id;
            this.form.title = this.item.title;
            this.form.description = this.item.description;
            this.form.kanban_id = this.item.kanban_id;
            this.form.kanban_status_id = this.item.kanban_status_id;
            this.form.order_id = this.item.order_id;
            this.form.due_date = this.item.due_date;
            this.method = 'patch';
        } else {
            this.form.kanban_id = this.status.kanban_id;
            this.form.kanban_status_id = this.status.id;
            this.form.order_id = this.status.items.length;
        }
        this.$initTinyMCE([
            "autolink link"
        ], );
    },
    methods: {
        open(modal, relationKey) {
            if (this.method === 'post'){
                axios.post(this.requestUrl, this.form) //persist item to get kanbanItem.id
                    .then(res => {
                         this.form.populate( res.data.message );
                         this.method = 'patch'; //now use edit mode
                         if (relationKey === 'referenceable'){
                             this.$modal.show(modal, { 'referenceable_type': 'App\\KanbanItem', 'referenceable_id': this.form.id });
                         } else {
                            this.$modal.show(modal, { 'subscribable_type': 'App\\KanbanItem', 'subscribable_id': this.form.id });
                        }
                    })
                    .catch(error => { // Handle the error returned from our request
                        this.form.errors = error.response.data.errors;
                    });
            } else {
                if (relationKey === 'referenceable'){
                    this.$modal.show(modal, { 'referenceable_type': 'App\\KanbanItem', 'referenceable_id': this.form.id });
                } else {
                    this.$modal.show(modal, { 'subscribable_type': 'App\\KanbanItem', 'subscribable_id': this.form.id });
                }
            }
        },
        submit() {
            let method = this.method.toLowerCase();
            this.form.description = tinyMCE.get('description').getContent();
            if (method === 'patch') {
                    axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                     .then(res => { // Tell the parent component we've updated a task
                             this.$emit("item-updated", res.data.message);
                        })
                     .catch(error => { // Handle the error returned from our request
                         console.log(error);
                        });
            } else {
                axios.post(this.requestUrl, this.form)
                     .then(res => { // Tell the parent component we've added a new task and include it
                            this.$emit("item-added", res.data.message);
                        })
                     .catch(error => { // Handle the error returned from our request
                            console.log(error);
                        });
            }
        },
    },
    components: {
        DatePicker
    },
};
</script>
