<template>
    <div class="card">
        <div class="card-header px-3 py-2" :style="{ backgroundColor: item.color, color: textColor }">
            <div class="card-tools">
                <div v-if="(editable == 1 && editor === false && onlyEditOwnedItems !== 1) || (editable == 1 && $userId == item.owner_id )"
                     class="btn btn-flat py-0 px-2 "
                     :id="'kanbanItemDropdown_'+index"
                     style="background-color: transparent;"
                     data-toggle="dropdown"
                     aria-expanded="false">
                    <i class="fas fa-ellipsis-v"
                       :style="{ 'text-color': textColor }"></i>
                    <div class="dropdown-menu" x-placement="top-start">
                        <button :name="'kanbanItemEdit_'+index"
                                class="dropdown-item text-secondary  py-1"
                                @click="edit()">
                            <i class="fa fa-pencil-alt mr-4"></i>
                            {{ trans('global.kanbanItem.edit') }}
                        </button>
                        <button
                            v-can="'external_medium_create'"
                            :name="'kanbanItemAddMedia_'+index"
                            class="dropdown-item text-secondary  py-1"
                            @click="addMedia()">
                            <i class="fa fa-folder-open mr-4"></i>
                            {{ trans('global.media.title_singular') }}
                        </button>
                        <!--                      <button :name="'kanbanItemShare_'+index"
                                                      class="dropdown-item text-secondary  py-1"
                                                      @click="open('subscribe-modal')">
                                                <i class="fa fa-share-alt mr-4"></i>
                                                {{ trans('global.share') }}
                                              </button>-->
                        <hr class="my-1">
                        <button
                            v-can="'kanban_delete'"
                            :name="'kanbanItemDelete_'+index"
                            class="dropdown-item py-1 text-red"
                            @click="confirmItemDelete()">
                            <i class="fa fa-trash mr-4"></i>
                            {{ trans('global.delete') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="pb-1" >
                <span v-if="editor !== false">
                    <span
                        class="pull-left"
                        style="border-style: solid; border-width: 1px; border-radius: 20px; padding: 2px 2px 0 2px; height: 25px;"
                        :style="{borderColor: textColor }">
                        <color-picker-input
                            :id="'colorPicker_'+index"
                            v-if="editor !== false"
                            v-model="form.color">
                        </color-picker-input>
                    </span>
                    <input
                        :id="'title_'+index"
                        type="text"
                        v-model="form.title"
                        class="w-100"
                        style="font-size: 1.1rem; font-weight: 400; border: 0; border-bottom: 1px; border-style:solid; margin: 0;"
                        :style="{ backgroundColor: item.color, color: textColor }"
                    />
                </span>
                <span v-else>
                        {{ item.title }}
                </span>
                <div style="margin-bottom: -4px;">
                    <span class="due-date">{{ postDate() }}</span>
                    <span v-if="expired" class="badge badge-secondary">{{ trans('global.kanbanItem.expired') }}</span>
                </div>
                <span class="" style="font-size: .5rem">
                    {{ item.created_at }}
                </span>
            </div>

        </div>
        <div class="card-body p-0">
            <textarea
                v-if="editor !== false"
                :id="'description_' + item.id"
                :name="'description_' + index"
                :placeholder="trans('global.kanbanItem.fields.description')"
                class="form-control description my-editor "
                v-model.trim="form.description"
            ></textarea>
            <div v-else-if="item.description !== null"
                 class="text-muted small px-3 py-2"
                 v-html="form.description">
            </div>
            <date-picker
                v-if="editor !== false"
                class="w-100 mt-3"
                v-model="due_date"
                type="datetime"
                valueType="YYYY-MM-DD HH:mm:ss"
                :placeholder="trans('global.kanbanItem.due_date')">
            </date-picker>
            <button v-if="editor !== false"
                    :name="'kanbanItemSave_' + index"
                    class="btn btn-primary p-2 m-2"
                    @click="submit()">
                {{ trans('global.save') }}
            </button>

            <!--          <kanbanTask
                          class="mx-3 "
                          :tasks="item.task_subscription">
                      </kanbanTask>-->

            <mediaCarousel
                v-if="item.media_subscriptions.length > 0"
                :subscriptions="item.media_subscriptions"
                :width="width -16"
            ></mediaCarousel>
        </div>

        <div class="card-footer px-3 py-2"
             :class="{'border-top-0':item.description === null}"
        >
            <div class="d-flex">
                <avatar class="pull-left contacts-list-img flex-fill"
                        data-toggle="tooltip"
                        :title="item.owner.firstname + ' ' + item.owner.lastname"
                        :username="item.owner.username"
                        :firstname="item.owner.firstname"
                        :lastname="item.owner.lastname"
                        :size="25"
                ></avatar>
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
//import mediaCarousel from '../media/MediaCarousel';
/*import avatar from "../uiElements/Avatar";
import Modal from "./../uiElements/Modal";
import Reaction from "../reaction/Reaction";
import Comments from "./Comments";*/

export default {
    props: {
        'item': Object,
        'index': String,
        'width': Number,
        'commentable': false,
        'onlyEditOwnedItems': false,
        'likable': true,
        'editable': true
    },
    data() {
        return {
            new_media: null,
            show_comments: false,
            editor: false,
            due_date: null,
            expired: false,
            form: new Form({
                'id':'',
                'title':'',
                'description': '',
                'kanban_id': '',
                'kanban_status_id': '',
                'order_id': 0,
                'color': '#F4F4F4',
                'due_date': ''
            }),
        };
    },
    computed:{
        textColor: function(){
            if(this.item.color == "" || this.item.color == null ) return;
            let hex = this.item.color.substring(1, 7);
            let r = parseInt(hex.slice(0, 2), 16),
                g = parseInt(hex.slice(2, 4), 16),
                b = parseInt(hex.slice(4, 6), 16);

            // Return light or dark class based on contrast calculation
            return ((r * 0.299 + g * 0.587 + b * 0.114) > 186) ? '#333333' : '#FFFFFF';
        }
    },
    methods: {
        confirmItemDelete(){
            $('#itemModal_'+ this.index).modal('show');
        },
        deleteItem() {
            axios.delete("/kanbanItems/" + this.item.id)
                .then(res => {
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
                this.$initTinyMCE([
                    "autolink link"
                ] );
            });

        },
        submit() {
            let form = new Form({
                'id': this.item.id,
                'title': this.item.title,
                'description': tinyMCE.get('description_'+this.item.id).getContent(),
                'kanban_id': this.item.kanban_id,
                'kanban_status_id': this.item.kanban_status_id,
                'due_date': this.due_date,
                'order_id': this.item.order_id,
                'color': this.item.color
            });

            axios.patch('/kanbanItems/' + form.id, form)
                .then(res => { // Tell the parent component we've updated an item
                    tinyMCE.get('description_'+this.item.id).remove();
                    this.form = res.data.message; //selfUpdate
                    this.$emit("item-updated", res.data.message);
                })
                .catch(error => { // Handle the error returned from our request
                    this.form.errors = error.response.data.errors;
                });
            this.editor = false;
            MathJax.startup.defaultReady();
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
                    this.$emit("item-updated", res.data.message);
                    //this.item = res.data.message;
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        postDate() {
            if (this.due_date == null) return undefined;

            const date = new Date(this.due_date.replace(/-/g, "/"));
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
        this.due_date = this.item.due_date;
        this.$eventHub.$on('reload_kanban_item', (e) => {
            if (this.item.id == e.id) {
                this.reload();
            }
        });
        this.$nextTick(() => {
            MathJax.startup.defaultReady();
        })
    },
    watch: {
        form: function (){
            MathJax.startup.defaultReady();
        }
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