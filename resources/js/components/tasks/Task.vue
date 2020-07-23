<template>
    <div :id="'#task_'+task.id" >
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="m-0">
                        <i class="fa fa-tasks mr-1"></i> {{ task.title }}
                    </h5>
                </div>
                
                <div v-can="'task_edit'" class="card-tools pr-2">
                    <a @click.prevent="$modal.show('task-modal', {'method': 'patch', 'id': '{{ task.id }}'})" >
                        <i class="far fa-edit"></i>
                    </a> 
                </div>
                
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <span v-html="task.description"></span>
                <hr>
                <div class="row">
                    <span class="col-md-6 col-sm-12">
                        <i class="fa fa-calendar pr-1"></i>
                        {{ task.start_date }}
                    </span>        
                    <span class="col-md-6 col-sm-12">
                        <i class="fa fa-calendar-check pr-1"></i>
                        {{ task.start_date }}
                    </span> 
                </div>
            </div>
            <!-- /.card-body -->
            
            <div v-can="'task_edit'" class="card-footer">
                <small class="float-right">
                    {{ task.updated_at }}
                </small> 
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="pull-right " v-can="'task_edit'">

                    <button type="button" 
                            class="btn btn-tool  pt-3"
                            data-toggle="dropdown" 
                            aria-expanded="true">
                        <i class="fa fa-plus"></i>
                    </button>
                    <div class="dropdown-menu" 
                         x-placement="top-start" 
                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -2px, 0px);">
                        <button class="dropdown-item" @click.prevent="open('medium-create-modal', 'referenceable');">
                            <i class="fa fa-file"></i>
                            <span class="ml-2">{{ trans('global.media.title') }}</span>
                        </button>
                        <button class="dropdown-item" @click.prevent="open('subscribe-objective-modal', 'referenceable');">
                            <i class="fa fa-bullseye"></i>
                            <span class="ml-2">{{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}</span>
                        </button>
                        <button class="dropdown-item" @click.prevent="open('content-create-modal', 'referenceable');">
                            <i class="fa fa-file-alt"></i>
                            <span class="ml-2">{{ trans('global.content.create') }}</span>
                        </button>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item" @click.prevent="edit()">
                            <i class="fa fa-edit"></i>
                            <span class="ml-2">{{ trans('global.task.edit') }}</span>
                        </button>
                        <button class="dropdown-item text-danger" @click.prevent="destroy()">
                            <i class="fa fa-trash"></i>
                            <span class="ml-2">{{ trans('global.delete') }}</span>
                        </button>
                    </div>
                </div>
                <ul class="nav nav-pills">
                    <li class="nav-item small"><a class="nav-link active show" href="#activity" data-toggle="tab">{{ trans('global.subscription-billing') }}</a></li>
                    <li class="nav-item small">
                        <a class="nav-link" 
                           v-bind:href="'#task_contents_'+task.id" 
                           data-toggle="tab">{{ trans('global.content.title') }}</a>
                    </li> 
                    <li class="nav-item small">
                        <a class="nav-link" 
                           v-bind:href="'#task_objectives_'+task.id" 
                           data-toggle="tab">
                            {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                        </a>
                    </li>
                    <li class="nav-item small">
                        <a class="nav-link" 
                           v-bind:href="'#task_media_'+task.id" 
                           data-toggle="tab">
                            {{ trans('global.media.title') }}
                        </a>
                    </li>
                    <li class="nav-item small"><a class="nav-link" href="#timeline" data-toggle="tab">{{ trans('global.history') }}</a></li>
                </ul>
                
            </div><!-- /.card-header -->

            <div class="card-body">

                <div class="tab-content">
                     <div class="tab-pane active show" id="activity">
                          <task-timeline :task="task"></task-timeline>           

                     </div><!-- /.tab-pane -->
                     <div class="tab-pane " 
                         v-bind:id="'task_contents_'+task.id"  >

                        <ul class="nav nav-pills">
                            <li class="nav-item" v-for="(item,index) in contentCategories" v-bind:value="'category_'+item.id">
                                <a class="nav-link show small" 
                                   v-bind:href="'#category_'+item.id" 
                                   :class="{ 'active': index === 0 }"
                                   data-toggle="tab">{{ item.title }}</a>
                            </li>
                        </ul>
                        <hr class="mt-1">
                        <div class="tab-content">
                            <div class="tab-pane" 
                                v-for="(item,index) in contentCategories" 
                                v-bind:id="'category_'+item.id"
                                :class="{ 'active': index === 0 }">
                               <content-group  class="p-2"
                                   :contents="filterContent(item.id)"
                                   :category="item">
                               </content-group>
                           </div>
                        </div> 

                    </div>
                    <div class="tab-pane" 
                         v-bind:id="'task_media_'+task.id">
                        <span v-for="subscription in task.media_subscriptions">
                           <medium :subscription="subscription" :medium="subscription.medium" ></medium>  
                        </span> 
                    </div>
                    
                    <div class="tab-pane" 
                         v-bind:id="'task_objectives_'+task.id">
                        <objective-box 
                            v-for="terminal_subscription in task.terminal_objective_subscriptions" 
                            v-bind:key="terminal_subscription.id"
                            :objective="terminal_subscription.terminal_objective"
                            type="terminal"></objective-box>
                        <span class="clearfix"></span>
                        <span v-for="enabling_subscription in task.enabling_objective_subscriptions">
                            <objective-box 
                                :objective="enabling_subscription.enabling_objective.terminal_objective"
                                type="terminal"></objective-box>
                            <objective-box 
                                :objective="enabling_subscription.enabling_objective"
                                type="enabling"></objective-box>
                            <span class="clearfix"></span>
                        </span>
                    </div>
                    <div class="tab-pane" id="timeline"><!-- The timeline -->
                        
                    </div><!-- /.tab-pane -->

                </div><!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div><!-- /.nav-tabs-custom -->       
           
    </div>
  
</template>

<script>
    import ContentGroup from '../content/ContentGroup';
    import ObjectiveBox from '../objectives/ObjectiveBox';
    import Medium from '../media/Medium';
    
    export default {
        
        props: {
            'task': Object,
            'search': ''
        },
        data() {
            return {
                media: {},
                active: true
              
            };
        },
        methods: {
            open(modal, relationKey) {
                if (modal === 'absence-modal'){
                    this.$modal.show(modal, JSON.stringify(_.merge({ 'referenceable_type': 'App\\Task', 'referenceable_id': this.task.id}, this.task)));
                }
                else if (relationKey === 'referenceable'){
                    this.$modal.show(modal, { 'referenceable_type': 'App\\Task', 'referenceable_id': this.task.id });
                } else {
                    this.$modal.show(modal, { 'subscribable_type': 'App\\Task', 'subscribable_id': this.task.id });
                }
                //open tab
                switch (modal) {
                    case "subscribe-objective-modal": 
                        $('.nav-item a[href="#task_objectives_'+this.task.id+'"]').tab('show');
                    break;
                    case "content-create-modal": 
                        $('.nav-item a[href="#task_contents_1'+this.task.id+'"]').tab('show');
                    break;
                    case "task-modal": 
                        $('.nav-item a[href="#task_tasks_1'+this.task.id+'"]').tab('show');
                    break;
                }
               
            },
            edit() {
                 this.$modal.show('task-modal', { 'id': this.task.id, 'method': 'patch'});
            },
            async destroy(){
                try {   
                    this.location = (await axios.delete('/task/'+this.task.id)).data.message;
                } catch(error) {
                    alert(error);
                }
                location.reload(true);
            }, 
            
            filterContent(category){
                if (category === 1){
                    return [].concat(...this.task.content_subscriptions.filter(c => c.content.categories.find(cat => cat.id === category)))
                             .concat(...this.task.content_subscriptions.filter(c => c.content.categories.length === 0));
                } else {
                    return [].concat(...this.task.content_subscriptions.filter(c => c.content.categories.find(cat => cat.id === category)));
                }
                    
                
            },
            getUnique(arr, comp) {
                const unique = arr
                   .map(e => e[comp])
                   // store the keys of the unique objects
                   .map((e, i, final) => final.indexOf(e) === i && i)
                   // eliminate the dead keys & store unique objects
                   .filter(e => arr[e]).map(e => arr[e]);

                 return unique;
            },
            say: function (msg) {
                alert(msg);
            },
           
        },
        computed: {
            contentCategories: function() {
                if (this.task.content_subscriptions.length !== 0){
                    let categories = [].concat(...this.task.content_subscriptions
                                   .map(c => c.content.categories.map(cat =>({'id' : cat.id, 'title' : cat.title}))))
                                   .concat({'id' : 1, 'title' : 'Ohne Kategorie'}); //hack: default has to be set
                
                return this.getUnique(categories, 'id');
                }   
            },
        },
        
        mounted() {
            
        },   
        components: {
            Medium,
            ContentGroup,
            ObjectiveBox
        }
        
    }
</script>