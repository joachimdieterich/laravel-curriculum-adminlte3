<template>
    <div class="card mr-2">
        <div class="card-body px-3 py-2">
            <div class="form-group">
                <input
                    type="text" 
                    id="title"
                    name="title"
                    class="form-control"
                    v-model.trim="form.title"
                    placeholder="Titel"
                    required
                    />
                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
             </div>
             <div class="form-group mb-2">
                 <textarea
                 id="description" 
                 name="description" 
                 placeholder="Beschreibung"
                 class="form-control description my-editor "                  
                  v-model.trim="form.description"
                 ></textarea>
                 <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
            </div>
            <span v-if="item != null">
                <div v-if="item.media[0] != null">
                    <span v-if="item.media[0].mime_type == 'image/jpeg'">
    <!--                    <button class="btn btn-danger btn-sm pull-right top-0">
                          <small><i class="fa fa-trash"></i></small>
                       </button>-->
                        <img 
                        :src="'/media/'+item.media[0].id"
                        style="object-fit: cover; height:150;width:100%;"
                        class="pb-2"/> 
                    </span>

                    <embed v-else
                         :src="'/media/'+item.media[0].id" 
                         width="100%" 
                         height="150"
                         class="pb-2">
                </div>
            </span>
            <div v-if="form.title != ''">
                <button class="btn btn-block btn-outline-secondary mb-2" 
                        @click.stop.prevent="open('medium-create-modal', 'referenceable');">
                    <i class="fa fa-photo-video"></i>
                    <span class="ml-2">{{ trans('global.media.add') }}</span>
                </button>
                <button class="btn btn-block btn-outline-secondary mb-2" 
                         @click.stop.prevent="open('task-modal', 'subscribable');">
                    <i class="fas fa-tasks"></i>
                    <span class="ml-2">{{ trans('global.task.create') }}</span>
                </button>
            </div>

              

            <button
                @click="$emit('item-canceled')"
                type="reset"
                class="btn btn-default"
              >
                {{ trans('cancel') }}
            </button>
            <button
                class="btn btn-primary pull-right"
                @click="submit"
              >
                {{ trans('save') }}
            </button>
        </div> 
    </div>
</template>

<script>
import Form from 'form-backend-validation';
export default {
    
    props: {
      status: Object,
      item: Object
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
                    'order_id': 0
                }),
        };
    },
    mounted() {
        if ( this.item !== null ) {
            this.form.id = this.item.id;
            this.form.title = this.item.title;
            this.form.description = this.item.description;
            this.form.kanban_id = this.item.kanban_id;
            this.form.kanban_status_id = this.item.kanban_status_id;
            this.form.order_id = this.item.order_id;
            this.method = 'patch';
        } else {
            this.form.kanban_id = this.status.kanban_id;
            this.form.kanban_status_id = this.status.id;
            this.form.order_id = this.status.items.length;
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
            }       
        },
        submit() {
            var method = this.method.toLowerCase();
            if (method === 'patch') {
                    axios.patch(this.requestUrl += '/' + this.form.id, this.form)
                     .then(res => { // Tell the parent component we've added a new task and include it
                             this.$emit("item-updated", res.data.message);
                             
                        })
                     .catch(error => { // Handle the error returned from our request
                             this.form.errors = error.response.data.errors;
                        }); 
            } else {
                axios.post(this.requestUrl, this.form)
                     .then(res => { // Tell the parent component we've added a new task and include it
                            this.$emit("item-added", res.data.message);
                        })
                     .catch(error => { // Handle the error returned from our request
                             this.form.errors = error.response.data.errors;
                        }); 
            }
            
        },           
      
    }
};
</script>