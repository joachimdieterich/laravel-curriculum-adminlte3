<template>
    <div class="card">
        <div class="card-header px-3 py-2" :style="{ backgroundColor: item.color, color: textColor }">
            <div class="card-tools">

                <div v-if="editable" class="btn btn-flat py-0 px-2 "
                     style="background-color: transparent;"
                     data-toggle="dropdown"
                     aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                    <div class="dropdown-menu" x-placement="top-start">
                      <button class="dropdown-item text-secondary  py-1"
                              @click="edit()">
                        <i class="fa fa-pencil-alt mr-4"></i>
                        {{ trans('global.kanbanItem.edit') }}
                      </button>
                      <button class="dropdown-item text-secondary  py-1"
                              @click="addMedia()">
                        <i class="fa fa-folder-open mr-4"></i>
                        {{ trans('global.media.title_singular') }}
                      </button>
                      <button class="dropdown-item text-secondary  py-1"
                              @click="open('subscribe-modal')">
                        <i class="fa fa-share-alt mr-4"></i>
                        {{ trans('global.share') }}
                      </button>
                      <hr class="my-1">
                      <button
                          v-can="'kanban_delete'"
                          class="dropdown-item py-1 text-red"
                            @click="deleteItem()">
                            <i class="fa fa-trash mr-4"></i>
                            {{ trans('global.delete') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="pb-1" style="line-height: 1">
                {{ item.title }}<br/>
                <span class="" style="font-size: .5rem">
                    {{ item.created_at }}
                </span>
            </div>

        </div>
        <div class="card-body p-0">
          <div v-if="item.description !== null"
               class="text-muted small px-3 py-2">
            {{ item.description }}
          </div>

          <kanbanTask
              class="mx-3 "
              :tasks="item.task_subscription">
          </kanbanTask>

          <mediaCarousel
              v-if="item.media_subscriptions.length > 0"
              :subscriptions="item.media_subscriptions"
              :width="width"
          ></mediaCarousel>
        </div>

      <div class="card-footer px-3 py-2 border-top-0">
          <div class="d-flex">
              <avatar class="pull-left contacts-list-img flex-fill"
                      data-toggle="tooltip"
                      :title="item.owner.firstname + ' ' + item.owner.lastname"
                      :username="item.owner.username"
                      :firstname="item.owner.firstname"
                      :lastname="item.owner.lastname"
                      :size="25"
              ></avatar>
              <div @click="openComments" class="position-relative flex-grow-0">
                  <i  class="fa fa-comment pointer"></i>
                  <span class="comment-count mt-1 small" v-if="comments.length > 0">{{ comments.length }}</span>
              </div>
          </div>

      </div>

        <div class="comments p-3" v-if="show_comments">
            <div class="comment d-flex mb-2 border-bottom" v-for="comment in comments">
                <div class="d-flex flex-column flex-fill">
                    <div class="d-flex flex-fill">
                        <div class="text-muted d-flex flex-column justify-content-between"
                             style="font-size: .5rem">
                            <avatar class="mr-2"
                                    data-toggle="tooltip"
                                    :title="comment.user.username"
                                    :username="comment.user.username"
                                    :firstname="comment.user.firstname"
                                    :lastname="comment.user.lastname"
                                    :size="20"
                            ></avatar>
                        </div>
                        <div class="text-sm flex-fill">
                            <!--span class="font-weight-bold" style="font-size: .5rem">{{ comment.user.username }}</span><br/-->
                            {{ comment.comment }}
                        </div>
                    </div>
                    <div>
                        <span class="text-muted" style="font-size:.5rem">{{ comment.created_at }}</span>
                    </div>
                </div>
                <div class="ml-3">
                    <button class="btn" @click="deleteComment(comment.id)"><i class="fa fa-x text-danger pointer"></i></button>
                </div>
            </div>
            <div class="d-flex text-sm">
                <input type="text" v-model="new_comment" class="form-control text-sm mr-1" placeholder="Kommentar">
                <span @click="sendComment" class="btn btn-success" :disabled="new_comment != ''">Senden</span>
            </div>
        </div>

    </div>
</template>

<script>
import kanbanTask from './KanbanTask';
import mediaCarousel from '../media/MediaCarousel';
import avatar from "../uiElements/Avatar";

export default {
  props: {
    'item': Object,
    'width': Number,
      'editable': true
  },
  data() {
    return {
      new_media: null,
        comments: this.item.comments,
        show_comments: false,
        new_comment:''
    };
  },
    computed:{
        textColor: function(){
          if(this.item.color == "") return
          let hex = this.item.color.substring(1, 7);
          let r = parseInt(hex.slice(0, 2), 16),
              g = parseInt(hex.slice(2, 4), 16),
              b = parseInt(hex.slice(4, 6), 16);

          // Return light or dark class based on contrast calculation
          return ((r * 0.299 + g * 0.587 + b * 0.114) > 186) ? '#333333' : '#FFFFFF';
      }
    },
  methods: {
    deleteItem() {
      axios.delete("/kanbanItems/" + this.item.id)
          .then(res => { // Tell the parent component we've added a new task and include it
            this.$emit("item-destroyed", this.item);
          })
          .catch(err => {
            console.log(err.response);
          });
    },
    edit() {
      this.$emit("item-edit", this.item);
    },
      openComments(){
        this.show_comments = !this.show_comments;
      },

      sendComment(){
          axios.post("/kanbanItemComment/", {
              'comment': this.new_comment,
              'kanban_item_id': this.item.id
          })
              .then(res => {
                  this.comments = res.data.data;
              })
              .catch(err => {
                  console.log(err.response);
              });
      },
      deleteComment(id){
          axios.delete("/kanbanItemComment/" + id)
              .then(res => { // Tell the parent component we've added a new task and include it
                  this.comments = res.data.data;
              })
              .catch(err => {
                  console.log(err.response);
              });

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
            'callbackParentComponent': 'kanbanBoard',
            'callbackComponent': 'kanbanItemId' + this.item.id,
            'callbackFunction': 'reload',
          });
    },
    reload() {
      axios.get("/kanbanItems/" + this.item.id)
          .then(res => {
            this.item = res.data.message;
          })
          .catch(err => {
            console.log(err.response);
          });
    }
  },

        components: {
          kanbanTask,
          mediaCarousel,
          avatar
        }

    }
</script>
