<template>
    <div class="card">
        <div class="card-header px-3 py-2">
            <div class="card-tools">
                <img
                    class="img-circle color-white"
                    style="height: 1.6rem"
                    :src="avatar"/>
                <div class="btn btn-flat py-0 px-2 "
                     style="background-color: transparent;"
                     data-toggle="dropdown"
                     aria-expanded="false">
                    <i class="text-muted fas fa-ellipsis-v"></i>
                    <div class="dropdown-menu" x-placement="top-start">
                        <button class="dropdown-item py-1"
                                @click="edit()">
                            <i class="fa fa-pencil-alt mr-4"></i>
                            {{ trans('global.kanbanItem.edit') }}
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

            <span v-if="item.media[0] != null">
                <medium-renderer
                    :medium="item.media[0]"
                    :width="252"
                ></medium-renderer>
            </span>
        </div>


        <div class="card-footer px-3 py-2 border-top-0">
            <span class="text-muted pull-right"
                  style="font-size: .6rem">{{item.created_at}}</span>
            <!--<span class="float-right badge bg-gray-light badge-btn mt-1 small">KanbanItem</span>-->
        </div>

    </div>
</template>

<script>
    import kanbanTask from './KanbanTask';
    import mediumRenderer from '../media/MediaRenderer';

    export default {
        props: {
            'item': Object,
            'width': Number

        },
        data() {
            return {
                avatar: null,

            };
        },
        methods: {
            deleteItem(){
                axios.delete("/kanbanItems/"+this.item.id)
                            .then(res => { // Tell the parent component we've added a new task and include it
                                this.$emit("item-destroyed", this.item);
                            })
                            .catch(err => {
                                console.log(err.response);
                             });
            },
            edit(){
                this.$emit("item-edit", this.item);
            },


        },
        created(){
            axios.get("/users/" + this.item.owner_id + "/avatar")
                 .then(res =>  {
                     this.avatar =  res.data.avatar.encoded;
                });
        },

        components: {
            kanbanTask,
            mediumRenderer
        }

    }
</script>
