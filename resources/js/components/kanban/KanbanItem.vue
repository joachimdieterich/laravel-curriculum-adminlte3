<template>
    <div class="card">
        <div class="card-header px-3 py-2" :style="{ backgroundColor: item.color, color: textColor }">
            <div class="card-tools">
                <div v-if="
                        (editable == true && item.editable == true && editor == false && onlyEditOwnedItems != true) ||
                        (editable == true && item.editable == true && $userId == item.owner_id  && editor == false) ||
                        (item.editable == true && $userId == kanban_owner_id  && editor == false) ||
                        ($userId == kanban_owner_id  && editor == false)"
                    class="float-right py-0 px-2 pointer"
                    :id="'kanbanItemDropdown_'+index"
                    style="background-color: transparent;"
                    data-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="fas fa-ellipsis-v"
                       :style="{ 'text-color': textColor }"></i>

                    <div class="dropdown-menu" x-placement="top-start">
                        <button :name="'kanbanItemEdit_'+index"
                                class="dropdown-item text-secondary py-1"
                                @click="edit()"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.kanbanItem.edit') }}
                        </button>
                        <button
                            v-can="'external_medium_create'"
                            :name="'kanbanItemAddMedia_'+index"
                            class="dropdown-item text-secondary py-1"
                            @click="addMedia()"
                        >
                            <i class="fa fa-folder-open mr-2"></i>
                            {{ trans('global.media.title_singular') }}
                        </button>
                        <button :name="'kanbanItemCopy_' + index"
                            class="dropdown-item text-secondary py-1"
                            @click="confirmCopy()"
                        >
                            <i class="fa fa-copy mr-2"></i>
                            {{ trans('global.kanbanItem.copy') }}
                        </button>
                        <div v-if="(item.editable == 1 && $userId == item.owner_id) || ($userId == kanban_owner_id)">
                            <hr class="my-1">
                            <button
                                v-can="'kanban_delete'"
                                :name="'kanbanItemDelete_'+index"
                                class="dropdown-item py-1 text-red"
                                @click="confirmItemDelete()">
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.kanbanItem.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="((!item.locked && editable)|| $userId == item.owner_id) || $userId == kanban_owner_id "
                    class="float-right  py-0 px-1 mx-1 handle pointer" >
                    <i class="fa fa-arrows-up-down-left-right"
                       :style="{ 'text-color': textColor }"></i>
                </div>
            </div>
            <div class="pb-0" >
                <span v-if="editor !== false">
                    <span
                        class="pull-left"
                        style="border-style: solid; border-width: 1px; border-radius: 15px; padding: 1px;"
                        :style="{borderColor: textColor }">
                        <color-picker-input
                            :id="'colorPicker_'+index"
                            :triggerStyle="{width: '24px', height: '18px'}"
                            v-if="editor !== false"
                            v-model="form.color">
                        </color-picker-input>
                    </span>
                    <input
                        :id="'title_'+index"
                        type="text"
                        class="ml-2"
                        v-model="form.title"
                        style="width: 235px !important;font-size: 1.1rem; font-weight: 400; border: 0; border-bottom: 1px; border-style:solid; margin: 0;"
                        :style="{ backgroundColor: item.color, color: textColor }"
                    />
                </span>
                <div v-else>
                    {{ item.title }}
                    <div class="clearfix"
                         style="font-size: .5rem">
                        {{ item.created_at }}
                    </div>
                </div>
            </div>

        </div>
        <div class="card-body p-0">
            <div v-if="(editor == false)">
                <div v-if="item.description !== null "
                     class="text-muted small px-3 py-2"
                     v-html="form.description">
                </div>
            </div>

            <div v-if="(editor !== false)"
            class="p-2">
                <div class="pb-2">
                    <textarea
                        :id="'description_' + item.id"
                        :name="'description_' + index"
                        :placeholder="trans('global.kanbanItem.fields.description')"
                        class="form-control description my-editor "
                        v-model.trim="form.description"
                    ></textarea>
                </div>
                <div>
                    <b class="pt-2 pointer"
                       @click="() => (expand = !expand)">
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
                </div>
                <div
                    v-if="expand == true">
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
            </div>
            <button v-if="editor !== false"
                :name="'kanbanItemSave_' + index"
                class="btn btn-primary pull-right mb-2 mr-2"
                @click="submit()">
                {{ trans('global.save') }}
            </button>
            <!--          <kanbanTask
                          class="mx-3 "
                          :tasks="item.task_subscription">
                      </kanbanTask>-->

            <mediaCarousel
                class="clearfix"
                v-if="item.media_subscriptions.length > 0"
                :subscriptions="item.media_subscriptions"
                :width="width -16"
            ></mediaCarousel>
        </div>
        <div class="card-footer px-3 py-2"
             v-if="(editor === false) && ((item.visible_from != null) || (item.visible_until != null)) && (item.due_date != null)"
             :class="{'border-top-0':item.description === null}"
        >
            <div class="w-100 ">
                <div class="due-date pull-left">{{ postDate() }}</div>
                <span v-if="expired"
                      class="pull-right badge badge-secondary">
                    {{ trans('global.kanbanItem.expired') }}
                </span>
                <div v-if="(item.visible_from != null) || (item.visible_until != null)"
                    class="due-date pull-left">
                    {{ trans('global.visibility')}} {{ trans('global.timeFrom')}}:  {{ diffForHumans(item.visible_from)}} {{ trans('global.timeTo')}}: {{ diffForHumans(item.visible_until) }}
                </div>
            </div>

        </div>

        <div class="card-footer px-3 py-2"
             v-if="editor === false"
             :class="{'border-top-0':item.description === null}"
        >
            <div class="d-flex align-items-center">
               <avatar
                    :key="item.id + '_editor_' +item.owner.id"
                    :title="item.owner.firstname + ' ' + item.owner.lastname"
                    :username="item.owner.username"
                    :firstname="item.owner.firstname"
                    :lastname="item.owner.lastname"
                    :size="25"
                    class="contacts-list-img"
                    data-toggle="tooltip"
                ></avatar>
                <avatar
                    v-for="(editor_user, index) in editors"
                    v-if="editor_user != '' && $userId != 8"
                    :key="item.id + '_editor_' + index"
                    :title="editor_user.firstname + ' ' + editor_user.lastname"
                    :username="editor_user.username"
                    :firstname="editor_user.firstname"
                    :lastname="editor_user.lastname"
                    :size="25"
                    class="contacts-list-img"
                    data-toggle="tooltip"
                ></avatar>

                <span class="d-flex flex-fill"></span>
                <div v-if="commentable"
                     @click="openComments"
                     class=" position-relative pull-right mr-2">
                    <i  class="far fa-comments pointer with-comment-count"></i>
                    <span v-if="item.comments.length > 0"
                          class="comment-count mt-1 small bg-success" >
                      {{ item.comments.length }}
                  </span>
                </div>
                <Reaction
                    :model="item"
                    reaction="like"
                    url="/kanbanItems"
                />
            </div>
        </div>

        <Comments
            v-if="show_comments"
            :comments="item.comments"
            :model="item"
            :kanban_owner_id="kanban_owner_id"
            url="/kanbanItemComment"
        />
        <Modal
            :id="'itemModal_' + index"
            css="danger"
            :title="trans('global.kanbanItem.delete')"
            :text="trans('global.kanbanItem.delete_helper')"
            :ok_label="trans('global.kanbanItem.delete')"
            v-on:ok="deleteItem"
        />
    </div>
</template>

<script>
import Form from "form-backend-validation";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
/*import kanbanTask from './KanbanTask';*/
/*const kanbanTask =
    () => import('./KanbanTask');*/
const mediaCarousel =
    () => import('../media/MediaCarousel');
const avatar =
    () => import('../uiElements/Avatar');
const Modal =
    () => import('./../uiElements/Modal');
const Reaction =
    () => import('../reaction/Reaction');
const Comments =
    () => import('./Comments');
import moment from 'moment';
import axios from "axios";

export default {
    props: {
        'item': Object,
        'index': String,
        'width': Number,
        'commentable': false,
        'onlyEditOwnedItems': false,
        'likable': true,
        'editable': true,
        'kanban_owner_id': {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            new_media: null,
            show_comments: false,
            editor: false,
            expired: false,
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
            editors: {}
        };
    },
    computed:{
        textColor: function(){
            if(this.item.color == "" || this.item.color == null ) return;
            return this.$textcolor(this.item.color, '#333333');
        }
    },
    methods: {
        diffForHumans: function (date) {
            if (date == null){
                return '\u221E';
            } else {
                return moment(date).locale('de').fromNow();
            }
        },
        confirmItemDelete(){
            $('#itemModal_'+ this.index).modal('show');
        },
        deleteItem() {
            axios.delete("/kanbanItems/" + this.item.id)
                .then(() => {
                    this.$emit("item-destroyed", this.item);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        edit() {
            this.editor = true;
            this.form = this.item;
            this.$nextTick(() => {
                this.$initTinyMCE(
                    [
                        "autolink link"
                    ]
                );
            });

        },
        submit() {
            this.form.description = tinyMCE.get('description_'+this.item.id).getContent();

            axios.patch('/kanbanItems/' + this.form.id, this.form)
                .then(res => { // Tell the parent component we've updated an item
                    tinyMCE.get('description_'+this.item.id).remove();
                    this.form = res.data.message; //selfUpdate
                    this.$emit("item-updated", res.data.message);
                    MathJax.startup.defaultReady();
                })
                .catch(error => { // Handle the error returned from our request
                    this.form.errors = error.response.data.errors;
                });
            this.editor = false;

        },
        confirmCopy() {
            this.$eventHub.$emit('copy-item', this.item.id);
            $('#kanbanItemCopyModal').modal('show');
        },
        openComments(){
            this.show_comments = !this.show_comments;
        },
        open(modal) {
            this.$modal.show(modal, {
                'modelUrl': 'kanbanItem',
                'modelId': this.item.id,
                'shareWithGroups': false,
                'shareWithOrganizations': false
            });
        },
        addMedia() {
            this.$modal.show(
                'medium-create-modal',
                {
                    'referenceable_type': 'App\\\KanbanItem',
                    'referenceable_id': this.item.id,
                    'eventHubCallbackFunction': 'reload_kanban_item',
                    'eventHubCallbackFunctionParams': this.item.id,
                });
        },
        reload() { //after media upload
            axios.get("/kanbanItems/" + this.item.id)
                .then(res => {
                    //this.$emit("item-updated", res.data.message);
                    this.$eventHub.$emit("item-updated", res.data.message);
                    //this.item = res.data.message;
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        getEditors() { //after media upload
            axios.get("/kanbanItems/" + this.item.id + "/editors")
                .then(res => {
                    this.editors = res.data.editors;
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        postDate() {
            if (this.form.due_date == null) return undefined;

            const date = new Date(this.form.due_date.replace(/-/g, "/"));
            const dateFormat = {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };

            this.expired = new Date() > date;

            return date.toLocaleString([], dateFormat);
        },
    },
    mounted() {
        this.form = this.item;
        this.getEditors();
        //this.due_date = this.item.due_date;
        this.$eventHub.$on('reload_kanban_item', (e) => {
            if (this.item.id == e.id) {
                this.reload();
            }
        });
        this.$eventHub.$on('filter', (filter) => {
            // always case insensitive
            const content = this.$el.innerText.toLowerCase();
            const search = filter.toLowerCase();

            this.$el.style.display = content.includes(search)
                ? 'flex'
                : 'none';
        });

        this.$nextTick(() => {
            MathJax.startup.defaultReady();
        });

    },
    watch: {
        form: function (){
            MathJax.startup.defaultReady();
        },
    },

    components: {
        Comments,
        Reaction,
        /*kanbanTask,*/
        mediaCarousel,
        avatar,
        Modal,
        DatePicker
    }
}
</script>
<style scoped>
.due-date {
    color: #6c757d;
    font-size: 12px;
    font-weight: 600;
}
.badge {
    border: 1px solid #dc3545;
    background-color: #fff;
    color: #dc3545;
    font-size: 10px;
    line-height: 11px;
    vertical-align: middle;
}
</style>
