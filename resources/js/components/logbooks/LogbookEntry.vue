<template>
    <!-- timeline item -->
    <div :id="'#logbook_'+entry.id" 
        class="card collapsed-card col-12"
        :style="isActive"
        >
        <div class="user-block p-2"
            data-card-widget="collapse" 
            :data-target="'#logbook_body_'+entry.id" 
            aria-expanded="true">

            <span class="username ml-0">
                
                <div class="pull-right " v-can="'logbook_entry_edit'">

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
                        <button class="dropdown-item" @click.prevent="open('absence-modal', 'referenceable');">
                            <i class="fa fa-user-times"></i>
                            <span class="ml-2">{{ trans('global.absences.create') }}</span>
                        </button>
                        <button class="dropdown-item" @click.prevent="edit()">
                            <i class="fa fa-edit"></i>
                            <span class="ml-2">{{ trans('global.logbookEntry.edit') }}</span>
                        </button>
                    </div>
                </div>
                <span href="#">{{ entry.title }}</span> 
                <span class="description ml-0">{{ postDate() }}</span>
            </span>
            
        </div>

        <div class="card-body p-0 collapse" :id="'#logbook_body_'+entry.id" >
            <hr class="m-1">
            <span class="clearfix"></span>

            <ul class="nav nav-pills">
                <li class="nav-item small">
                    <a class="nav-link active show" 
                       v-bind:href="'#logbook_description_'+entry.id" 
                       data-toggle="tab">
                        {{ trans('global.logbook.fields.description') }}
                    </a>
                </li>
                <li class="nav-item small">
                    <a class="nav-link" 
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
                       data-toggle="tab">{{ trans('global.absences.title') }}</a>
                </li>
            </ul>  
            <span class="clearfix"></span>
            <hr class="m-1">


            <div class="card-body p-2">
                <div class="tab-content">
                    <!-- tab-pane -->
                    <div class="tab-pane active show" 
                         v-bind:id="'logbook_description_'+entry.id">
                         <span class="" v-html="entry.description"></span>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" 
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
                            v-bind:key="terminal_subscription.id"
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
                    <div class="tab-pane show" 
                         v-bind:id="'logbook_tasks_'+entry.id"  
                         >
                         <task-list  class="p-2"
                            :tasks="entry.task_subscription">
                         </task-list>   

                    </div>
                    <!-- /.tab-pane -->
                    <!-- tab-pane -->
                    <div class="tab-pane show" 
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
            'logbook': Object,
            'entry': Object,
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
                   // alert(JSON.stringify(_.merge({ 'referenceable_type': 'App\\LogbookEntry', 'referenceable_id': this.entry.id}, this.logbook)));
                   
                    this.$modal.show(modal, JSON.stringify(_.merge({ 'referenceable_type': 'App\\LogbookEntry', 'referenceable_id': this.entry.id}, this.logbook)));
                }
                else if (relationKey === 'referenceable'){
                    this.$modal.show(modal, { 'referenceable_type': 'App\\LogbookEntry', 'referenceable_id': this.entry.id });
                } else {
                    this.$modal.show(modal, { 'subscribable_type': 'App\\LogbookEntry', 'subscribable_id': this.entry.id });
                }
                //open tab
                switch (modal) {
                    case "logbook-subscribe-objective-modal": 
                        $('.nav-item a[href="#logbook_objectives_'+this.entry.id+'"]').tab('show');
                    break;
                    case "content-create-modal": 
                        $('.nav-item a[href="#logbook_contents_1'+this.entry.id+'"]').tab('show');
                    break;
                    case "task-modal": 
                        $('.nav-item a[href="#logbook_tasks_1'+this.entry.id+'"]').tab('show');
                    break;
                }
               
            },
            edit() {
                 this.$modal.show('logbook-entry-modal', { 'id': this.entry.id, 'method': 'patch'});
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
            say: function (msg) {
                alert(msg);
            },
            postDate() {
                var start = new Date(this.entry.begin.replace(/-/g, "/"));
                var end   = new Date(this.entry.end.replace(/-/g, "/"));

                if (start.toDateString() === end.toDateString()) {
                  return this.entry.begin + " - " + end.toLocaleTimeString();
                } else {
                  return this.entry.begin + " - " + this.entry.end;
                }
               
            },
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
            isActive: function(){
                if (this.entry.title.toLowerCase().indexOf(this.search.toLowerCase()) === -1){
                    return "display:none";
                } else {
                    return "";
                }
            }
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