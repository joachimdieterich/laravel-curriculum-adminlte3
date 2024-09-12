<template>
    <div class="card mr-3">
        <div class="card-header px-3 py-2" :style="{ backgroundColor: form.color, color: textColor }">
            <span
                class="pull-left"
                style="border-style: solid; border-width: 1px; border-radius: 15px; padding: 1px;"
                :style="{borderColor: textColor }">
                <color-picker-input
                    :triggerStyle="{width: '24px', height: '24px'}"
                    v-model="form.color"
                    style="height: 24px;"
                ></color-picker-input>
            </span>
            <input
                :id="'title_' + component_id"
                type="text"
                v-model="form.title"
                class="ml-2"
                :class="{ 'missing-input': highlightTitleInput }"
                @input="highlightTitleInput = false"
                style="width: 235px !important;font-size: 1.1rem; font-weight: 400; border: 0; border-bottom: 1px; border-style:solid; margin: 0;"
                :style="{ backgroundColor: form.color, color: textColor }"
            />
        </div>
        <div class="card-body p-2">
            <div class="pb-2">
                <textarea
                    id="description"
                    name="description"
                    :placeholder="trans('global.kanbanItem.fields.description')"
                    class="form-control description my-editor "
                    v-model.trim="form.description"
                ></textarea>
            </div>
            <div class="pb-2">
                <b class="pt-2 pointer"
                   @click="() => (this.expand = !this.expand)">
                    {{ trans('global.settings')}}
                    <span class="pull-right">
                        <i v-if="expand == true"
                           class="fa fa-caret-up"
                        ></i>
                        <i v-else
                           class="fa fa-caret-down"
                        ></i>
                    </span>
                </b>
                <span
                v-if="expand == true">
                    <hr class="mt-0">
                    <div class="form-group ">
                        <date-picker
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
                </span>
            </div>
            <div class="pb-2">
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
            component_id: this._uid,
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
            expand: false,
            highlightTitleInput: false,
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
            this.form.locked = this.status.locked;
            this.form.editable = this.status.editable;
        }
        this.$initTinyMCE(
            [
                "autolink link lists table"
            ],
            null,
            "bold underline italic | alignleft aligncenter alignright ",
            "bullist numlist outdent indent |  table mathjax link ",
        );
    },
    computed:{
        textColor: function(){
            return this.$textcolor(this.form.color, '#333333');
        }
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
            if (this.form.title == null || this.form.title == ""){
                const titleInput = document.getElementById('title_' + this.component_id);
                titleInput.focus();
                this.highlightTitleInput = true;
                return;
            }
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

<style scoped>
.missing-input {
    border-color: red !important;
}
</style>
