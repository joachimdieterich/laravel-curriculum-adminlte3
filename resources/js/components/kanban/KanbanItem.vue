<template>
    <div class="card">
        <div class="card-header px-3 py-2">
            <div class="card-tools">

                <div class="btn btn-flat py-0 px-2 "
                     style="background-color: transparent;"
                     data-toggle="dropdown"
                     aria-expanded="false">
                    <i class="text-muted fas fa-ellipsis-v"></i>
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

            <div class="pb-1">{{ item.title }}</div>

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
        <avatar class="pull-left contacts-list-img"
                data-toggle="tooltip"
                :title="item.owner.firstname + ' ' + item.owner.lastname"
                :firstname="item.owner.firstname"
                :lastname="item.owner.lastname"
                :size="25"
        ></avatar>
        <span class="text-muted pull-right"
              style="font-size: .6rem">{{ item.created_at }}</span>
        <!--<span class="float-right badge bg-gray-light badge-btn mt-1 small">KanbanItem</span>-->
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
    'width': Number
  },
  data() {
    return {
      new_media: null,
    };
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
