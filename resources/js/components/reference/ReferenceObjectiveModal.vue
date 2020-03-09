<template>
    <modal 
        id="reference-objective-modal" 
        name="reference-objective-modal" 
        height="auto" 
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1000">
        <div class="card" 
             style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ trans('global.referenceable_types.objective') }}
                 </h3>
                
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                     </button>
                     <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                     </button>
                 </div> 
            </div>
            
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                
                <div class="form-group ">
                    <label for="curriculua">
                        {{ trans('global.curriculum.title_singular') }}
                    </label>
                    
                    <multiselect :options="curricula" 
                                :multiple="false" 
                                :close-on-select="true" 
                                :clear-on-select="false" 
                                :preserve-search="true" 
                                v-model="curriculum"
                                placeholder="Select" 
                                label="title" 
                                track-by="id" 
                                :preselect-first="true"
                                @input="loadObjectives">
                       <template slot="selection" slot-scope="{ values, search, isOpen }">
                           <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">
                               {{ value.length }} options selected
                           </span>
                       </template>
                   </multiselect>
                    
                    
                    
                </div>
                <div class="form-group ">
                    <label for="terminalObjectives">
                        {{ trans('global.terminalObjective.title_singular') }}
                    </label>
                    <multiselect :options="terminalObjectives" 
                                :multiple="false" 
                                :close-on-select="true" 
                                :clear-on-select="false" 
                                :preserve-search="true" 
                                v-model="terminalObjective"
                                placeholder="Select" 
                                label="title" 
                                track-by="id" 
                                :preselect-first="true"
                                @input="loadEnabling">
                       <template slot="selection" slot-scope="{ values, search, isOpen }">
                           <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">
                               {{ value.length }} options selected
                           </span>
                       </template>
                   </multiselect>
                   
                </div>
                <div class="form-group ">
                    <label for="enablingObjectives">
                        {{ trans('global.enablingObjective.title_singular') }}
                    </label>
                    <multiselect :options="enablingObjectives" 
                                :multiple="false" 
                                :close-on-select="true" 
                                :clear-on-select="false" 
                                :preserve-search="true" 
                                v-model="enablingObjective"
                                placeholder="Select" 
                                label="title" 
                                track-by="id" 
                                :preselect-first="true"
                                @input="setEnabling">
                       <template slot="selection" slot-scope="{ values, search, isOpen }">
                           <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">
                               {{ value.length }} options selected
                           </span>
                       </template>
                   </multiselect>
                </div>
            </div>
            
            <div class="card-footer">
                <span class="pull-right">
                     <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>  
        </div>
    </modal>
</template>

<script>
    import Multiselect from 'vue-multiselect'
    export default {
        data() {
            return {
                referenceable_type: null,
                referenceable_id: null,
                curricula: {},
                curriculum: {},
                curriculum_id: null,
                terminalObjectives: {},
                terminalObjective: {},
                terminal_objective_id: null,
                enablingObjectives: {},
                enablingObjective: {},
                enabling_objective_id: null,
                requestUrl: null,
            }
        },
        methods: {
            async loadCurricula() {
                try {  
                    this.curricula = (await axios.get('/curricula')).data.curricula;
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
                this.terminal_objective_id = null; //reset selection
                this.enabling_objective_id = null; 
            },
           
            async loadObjectives(value) {
                this.curriculum_id = value.id;
                try {    
                   this.terminalObjectives = (await axios.get('/curricula/'+value.id+'/objectives')).data.curriculum.terminal_objectives;
                   this.removeHtmlTags(this.terminalObjectives);
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
            },
            loadEnabling(value){
                let terminal = [].concat(...this.terminalObjectives.filter(ena => ena.enabling_objectives.find(e => e.terminal_objective_id === value.id)));
                this.enablingObjectives = terminal[0].enabling_objectives;
                this.removeHtmlTags(this.enablingObjectives);
                this.terminal_objective_id = value.id;
                this.requestUrl = '/terminalObjectiveSubscriptions';
            },
            setEnabling(value){
                this.enabling_objective_id = value.id;
                this.requestUrl = '/enablingObjectiveSubscriptions';
            },
            async submit() {
                try {
                    this.location = (await axios.post(this.requestUrl, {
                        'curriculum_id':            this.curriculum_id, 
                        'terminal_objective_id':    this.terminal_objective_id,
                        'enabling_objective_id':    this.enabling_objective_id,
                        'subscribable_type':        this.referenceable_type,
                        'subscribable_id':          this.referenceable_id,
                    })).data.message;
                    location.reload(true);
                    
                } catch(error) {
                    //
                }
            },
            
            beforeOpen(event) {
                this.loadCurricula();
                if (event.params.referenceable_type){
                    this.referenceable_type = event.params.referenceable_type;
                    this.referenceable_id = event.params.referenceable_id;
                }
             },
            beforeClose() {
            },
            opened(){
            },
            
            close(){
                this.$modal.hide('reference-objective-modal');
            },
            removeHtmlTags(array, field){
                var i;
                for (i = 0; i < array.length; i++) { 
                    array[i].title = array[i].title.replace(/<[^>]+>/g, '');
                }
            }
            
        },
        components: {
                Multiselect, 
            },
    }
</script>

