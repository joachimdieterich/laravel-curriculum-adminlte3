<template>
    <modal 
        id="enabling-objective-modal" 
        name="enabling-objective-modal" 
        height="auto" 
        :adaptive=true
        :scrollable=true
        :draggable=true
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 25000">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                <h3 class="card-title">
                    
                    <span v-if="method === 'post'">
                        {{ trans('global.create')  }} 
                    </span>
                    
                    <span v-if="method === 'patch'">
                        {{ trans('global.update')  }} 
                    </span>
                   
                    {{ trans('global.enablingObjective.title_singular') }}
                </h3>
                
                <div class="card-tools">
                   <button type="button" class="btn btn-tool" data-widget="remove" @click="$emit('close')">
                     <i class="fa fa-times"></i>
                   </button>
                 </div>
              
            </div>
            <form >
            <div class="card-body">
                <div class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.enablingObjective.fields.title') }} *</label>
                    <input 
                        type="text" id="title" 
                        name="title" 
                        class="form-control" 
                        v-model="form.title"
                        placeholder="Title" 
                        required
                        />
                     <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                </div>
                <div class="form-group "
                    :class="form.errors.description ? 'has-error' : ''"
                    >
                    <label for="description">{{ trans('global.enablingObjective.fields.description') }}</label>
                    <textarea 
                       id="description" 
                       name="description" 
                       class="form-control" 
                       v-model="form.description"
                       placeholder="Description" 
                       />
                    <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                </div>
               
            </div>
                <div class="card-footer">
                     <div class="form-group m-2">
                         <button type="button" class="btn btn-info" data-widget="remove" @click="$emit('close')">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-info" @click="submit()" >{{ trans('global.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';
    
    export default {
        data() {
            return {
                method: 'post',
                requestUrl: '/enablingObjectives',
                form: new Form({
                    'id': '',
                    'title': '',
                    'description': '',
                    'time_approach': '',
                    'curriculum_id': '',
                    'terminal_objective_id': '',
                }),
            }
        },
      
        
        methods: {
           
            beforeOpen(event) { 
                if (event.params.objective){
                    this.form.populate( event.params.objective );
                }
                             
                this.method = event.params.method;
               
            },
            
            beforeClose() { 
                console.log('close') 
            },
            
            submit() {
                var method = this.method.toLowerCase();
                
                if (method === 'patch'){
                    this.requestUrl += '/'+this.form.id;
                } 
                
                this.form.submit(method, this.requestUrl)
                    .then(response => alert('Your objective was created'+response.message.title))
                    .catch(response => alert('Your objective was not created'));
                //todo .then .catch
            }
        },
        created() {

        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>