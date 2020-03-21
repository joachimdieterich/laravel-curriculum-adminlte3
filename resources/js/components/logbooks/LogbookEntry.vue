<template>
    <!-- timeline item -->
    <div>
        <i class="fa fa-book bg-green"></i>
        <div class="timeline-item">
            <span class="time">
                <i class="fas fa-clock"></i>
                {{ entry.begin }} - {{ entry.end }}
            </span>
            <h3 class="timeline-header">
                <strong v-html="entry.title"></strong>
            </h3>

            <div class="timeline-body" v-html="entry.description"></div>
            
            <hr class="mb-1">
            <div class="card-header p-2">
                <div class="pull-left" v-can="'logbook_entry_edit'">
                    <div class="btn-group" >
                    <button type="button " 
                            class="btn btn-default btn-sm dropdown-toggle " 
                            style="background-color: transparent;color:#000;" 
                            data-toggle="dropdown" 
                            aria-expanded="false">
                      <span class="caret"></span>
                      {{ trans('global.logbookEntry.addition') }}
                    </button>
                    <div class="dropdown-menu" 
                         x-placement="top-start" 
                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -2px, 0px);">
                        <button class="dropdown-item" @click.prevent="open('medium-create-modal', 'referenceable');">
                            <i class="fa fa-file"></i>
                            <span class="ml-2">{{ trans('global.media.title') }}</span>
                        </button>
                        <button class="dropdown-item" @click.prevent="open('logbook-subscribe-objective-modal', 'referenceable');">
                            <i class="fa fa-bullseye"></i>
                            <span class="ml-2">{{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}</span>
                        </button>
                        <button class="dropdown-item" @click.prevent="open('content-create-modal', 'referenceable');">
                            <i class="fa fa-file-alt"></i>
                            <span class="ml-2">{{ trans('global.content.create') }}</span>
                        </button>
                        <button class="dropdown-item" @click.prevent="open('task-modal', 'subscribable');">
                            <i class="fa fa-tasks"></i>
                            <span class="ml-2">{{ trans('global.task.create') }}</span>
                        </button>
                        <button class="dropdown-item" @click="say('Nicht verfügbar.')">
                            <i class="fa fa-user-times"></i>
                            <span class="ml-2">{{ trans('global.userStatus.create') }}</span>
                        </button>
                        
                    </div>
                  </div>
                </div>
                
                <ul class="nav nav-pills pull-right">
                    <li class="nav-item small">
                        <a class="nav-link active show" 
                           v-bind:href="'#logbook_media_'+entry.id" 
                           data-toggle="tab">
                            {{ trans('global.media.title') }}
                        </a>
                    </li>
                    <li class="nav-item small">
                        <a class="nav-link" 
                           v-bind:href="'#logbook_objectives_'+entry.id" 
                           data-toggle="tab">
                            {{ trans('global.terminalObjective.title') }}/{{ trans('global.enablingObjective.title') }}
                        </a>
                    </li>
                    <li class="nav-item small">
                        <a class="nav-link" 
                           v-bind:href="'#logbook_contents_'+entry.id" 
                           data-toggle="tab">{{ trans('global.content.title') }}</a>
                    </li>
                    <li class="nav-item small">
                        <a class="nav-link" 
                           v-bind:href="'#logbook_tasks_'+entry.id" 
                           data-toggle="tab">{{ trans('global.task.title') }}</a>
                    </li>
                    <li class="nav-item small">
                        <a class="nav-link" 
                           v-bind:href="'#logbook_userStatuses_'+entry.id" 
                           data-toggle="tab">{{ trans('global.userStatus.title') }}</a>
                    </li>
                </ul>   
            </div>
            
            <div class="card-body p-2">
                <div class="tab-content">
                    <!-- tab-pane -->
                    <div class="tab-pane active show" 
                         v-bind:id="'logbook_media_'+entry.id">
                        
                        <span v-for="subscription in entry.media_subscriptions">
                           <medium :subscription="subscription" :medium="subscription.medium" ></medium>  
                        </span>
                        
                        
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" 
                         v-bind:id="'logbook_objectives_'+entry.id">
                        <objective-box 
                            v-for="terminal_subscription in entry.terminal_objective_subscriptions" 
                            :objective="terminal_subscription.terminal_objective"
                            type="terminal"></objective-box>
                        <span class="clearfix"></span>
                        <span v-for="enabling_subscription in entry.enabling_objective_subscriptions">
                            <objective-box 
                                :objective="enabling_subscription.enabling_objective.terminal_objective"
                                type="terminal"></objective-box>
                            <objective-box 
                                :objective="enabling_subscription.enabling_objective"
                                type="enabling"></objective-box>
                            <span class="clearfix"></span>
                        </span>
                        
                        
                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div class="tab-pane " 
                         v-bind:id="'logbook_contents_'+entry.id"  >
                        
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
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div class="tab-pane  show" 
                         v-bind:id="'logbook_tasks_'+entry.id"  
                         >
                         <task-list  class="p-2"
                            :tasks="entry.task_subscription">
                         </task-list>   
                        
                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div class="tab-pane  show" 
                         v-bind:id="'logbook_userStatuses_'+entry.id"  
                         >logbook_userStatuses</div>
                    <!-- /.tab-pane -->
                    
                </div>
            </div>
            
        </div>
    </div>
    <!-- END timeline item -->
</template>

<script>
    import ContentGroup from '../content/ContentGroup';
    import ObjectiveBox from '../objectives/ObjectiveBox';
    import TaskList from '../uiElements/TaskList';
    import Medium from '../media/Medium';
    
    export default {
        
        props: {
            'entry': Object,
        },
        data() {
            return {
                media: {},
              
            }
        },
        methods: {
            open(modal, relationKey) {
                if (relationKey == 'referenceable'){
                     this.$modal.show(modal, { 'referenceable_type': 'App\\LogbookEntry', 'referenceable_id' : this.entry.id});
                } else {
                     this.$modal.show(modal, { 'subscribable_type': 'App\\LogbookEntry', 'subscribable_id' : this.entry.id});
                     
                }
                //open tab
                switch (modal) {
                    case "logbook-subscribe-objective-modal": 
                        $('.nav-item a[href="#logbook_objectives_'+this.entry.id+'"]').tab('show')
                    break;
                    case "content-create-modal": 
                        $('.nav-item a[href="#logbook_contents_1'+this.entry.id+'"]').tab('show')
                    break;
                    case "task-modal": 
                        $('.nav-item a[href="#logbook_tasks_1'+this.entry.id+'"]').tab('show')
                    break;
                }
               
            },
           
            filterContent(category){
                if (category === 1){
                    return [].concat(...this.entry.content_subscriptions.filter(c => c.content.categories.find(cat => cat.id === category)))
                             .concat(...this.entry.content_subscriptions.filter(c => c.content.categories.length === 0));
                } else {
                    return [].concat(...this.entry.content_subscriptions.filter(c => c.content.categories.find(cat => cat.id === category)));
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
             
             deleteTask(task){
                 alert('deleteTask');
             },
             say: function (msg) {
                alert(msg);
              }

        },
        computed: {
            contentCategories: function() {
                if (this.entry.content_subscriptions.length !== 0){
                    let categories = [].concat(...this.entry.content_subscriptions
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
            ObjectiveBox,
            TaskList,
        }
        
    }
</script>