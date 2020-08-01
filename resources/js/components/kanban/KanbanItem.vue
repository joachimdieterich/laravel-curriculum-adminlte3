<template>
    <div class="card">
        <div class="card-header px-3 py-2">
            <div class="card-tools">
                <img
                    class="img-circle color-white"
                    style="height: 1.6rem"
                    :src="avatar"/>
                <button type="button" 
                        class="btn btn-flat py-0 px-2 " 
                        style="background-color: transparent;" 
                        data-toggle="dropdown" 
                        aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                    <div class="dropdown-menu" 
                        x-placement="top-start">
                       <span>
                           <button class="dropdown-item py-1" @click="edit()">
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
                       </span>
                    </div>
                </button>
                
            </div>
            
            <div class="pb-1">{{ item.title }}</div>
            
        </div>
        <div class="card-body p-0">
            <div class="text-muted small px-3 py-2">{{ item.description }}</div> 
            <div v-if="item.subscribable != null">
                <img v-if="item.subscribable.mime_type == 'image/jpeg'"
                 :src="'/media/'+item.subscribable.id"
                 style="object-fit: cover; height:150;"
                 :style="'width:' + (width - 20) +'px;'"/>
                <embed v-else
                     :src="'/media/'+item.subscribable.id" :width="(width - 20)" height="150" class="">
            </div>
        </div>

        
        <div class="card-footer pt-2 border-top-0">
            <span class="float-righttext-muted small">{{item.created_at}}</span>
            <span class="float-right badge bg-primary mt-1 small">KanbanItem</span>
        </div>

    </div>
</template>

<script>

    
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
        mounted() {
            
        },   
        components: {
           
        }
        
    }
</script>