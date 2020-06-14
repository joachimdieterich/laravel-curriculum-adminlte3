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
        style="z-index: 1100">
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
                
                <div v-if="method === 'post'" class="form-group ">
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
                <div v-if="method === 'post'" class="form-group ">
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
                <div v-if="method === 'post'" class="form-group ">
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
                
                <div class="form-group ">
                    <label for="description">{{ trans('global.description') }}</label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control description my-editor "
                        v-model="form.description"
                    ></textarea>
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
    import Form from 'form-backend-validation';
    import Multiselect from 'vue-multiselect'
    export default {
        data() {
            return {
                method: 'post',
                form: new Form({
                    'id': null,
                    'referenceable_type': null,
                    'referenceable_id': null,
                    'curriculum_id': null,
                    'terminal_objective_id': null,
                    'enabling_objective_id': null,
                    'description': null
                }),
                
                curricula: {},
                curriculum: {},
                terminalObjectives: {},
                terminalObjective: {},
                enablingObjectives: {},
                enablingObjective: {},
                referenceRequestUrl: null,
            }
        },
        methods: {
            
            async loadCurricula() {
                try {  
                    this.curricula = (await axios.get('/curricula')).data.curricula;
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
                this.form.terminal_objective_id = null; //reset selection
                this.form.enabling_objective_id = null; 
            },
           
            async loadObjectives(value) {
                this.form.curriculum_id = value.id;
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
                this.form.terminal_objective_id = value.id;
                this.requestUrl = this.referenceRequestUrl ? this.referenceRequestUrl : '/terminalObjectiveSubscriptions';
            },
            setEnabling(value){
                this.form.enabling_objective_id = value.id;
                this.requestUrl = this.referenceRequestUrl ? this.referenceRequestUrl : '/enablingObjectiveSubscriptions';
            },
            async submit() {
                try {
                    if (this.method === 'patch'){
                        await axios.patch('/references/'+this.form.id, {
                            'description' : tinyMCE.get('description').getContent(),
                        }).data.message;
                        this.close();
                    } else {
                        this.location = (await axios.post(this.requestUrl, {
                            'curriculum_id':         this.form.curriculum_id, 
                            'terminal_objective_id': this.form.terminal_objective_id,
                            'enabling_objective_id': this.form.enabling_objective_id,
                            'subscribable_type':     this.form.referenceable_type,
                            'subscribable_id':       this.form.referenceable_id,
                            'description' :          tinyMCE.get('description').getContent(),
                        })).data.message;
                    }
                    location.reload(true); 
                } catch(error) {
                    //
                }
            },
            
            beforeOpen(event) { 
                if (event.params.id){
                    this.method = "patch";
                    this.form.id = event.params.id;
                    this.form.description = event.params.description;
                } else {
                    this.method = "post";
                    this.loadCurricula();
                    if (event.params.referenceable_type){
                        this.form.referenceable_type = event.params.referenceable_type;
                        this.form.referenceable_id = event.params.referenceable_id;
                    }
                    if (event.params.requestUrl){
                        this.referenceRequestUrl = event.params.requestUrl;
                    }
                }
                
             },
            beforeClose() {
            },
            opened(){
                 this.$initTinyMCE();

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

