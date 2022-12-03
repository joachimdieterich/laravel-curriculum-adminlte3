<template>
    <div class="card">
        <div class="card-header px-3 py-2" :style="{ backgroundColor: item.color, color: textColor }">
            <div class="card-tools">
                <div v-if="editable && editor === false" class="btn btn-flat py-0 px-2 "
                     style="background-color: transparent;"
                     data-toggle="dropdown"
                     aria-expanded="false">
                    <i class="fas fa-ellipsis-v"
                       :style="{ 'text-color': textColor }"></i>
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
                    <span v-if="editor !== false">
                        <span   class="pull-left"
                                style="background-color: rgb(244, 244, 244); border-radius: 20px; padding: 2px 2px 0px 2px;">
                            <color-picker-input
                                v-if="editor !== false"
                                v-model="form.color">
                            </color-picker-input>
                        </span>
                        <input
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
                <br/>
                <span class="" style="font-size: .5rem">
                    {{ item.created_at }}
                </span>
            </div>

        </div>
        <div class="card-body p-0">
            <textarea
                v-if="editor !== false"
                :id="'description_'+item.id"
                name="description"
                placeholder="Beschreibung"
                class="form-control description my-editor "
                v-model.trim="form.description"
            ></textarea>
            <div v-else-if="item.description !== null"
                 class="text-muted small px-3 py-2"
                 v-html="form.description">
            </div>
            <button v-if="editor !== false"
                    class="btn btn-primary p-2 m-2"
                    @click="submit()">
                {{ trans('global.save') }}
            </button>

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
import Form from "form-backend-validation";

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
        new_comment:'',
        editor: false,
        form: new Form({
            'id':'',
            'title':'',
            'description': '',
            'kanban_id': '',
            'kanban_status_id': '',
            'order_id': 0,
            'color': '#F4F4F4'
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
            this.editor = true;
            this.$nextTick(() => {
                this.$initTinyMCE([
                    "autolink link example"
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
                  'order_id': this.item.order_id,
                  'color': this.item.color
              });

              axios.patch('/kanbanItems/' + form.id, form)
                  .then(res => { // Tell the parent component we've updated an item
                      tinymce.remove();
                      this.form = res.data.message; //selfupdate
                      this.$emit("item-updated", res.data.message);
                  })
                  .catch(error => { // Handle the error returned from our request
                      this.form.errors = error.response.data.errors;
                  });
              this.editor = false;
          },

          openComments(){
            this.show_comments = !this.show_comments;
          },

          sendComment(){
              axios.post("/kanbanItemComment", {
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
        mounted() {
            this.form = this.item;
        },

        components: {
          kanbanTask,
          mediaCarousel,
          avatar
        }
    }
</script>
