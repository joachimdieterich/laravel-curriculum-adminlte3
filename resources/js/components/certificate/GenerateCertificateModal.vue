<template>
    <modal 
        id="certificate-generate-modal" 
        name="certificate-generate-modal" 
        height="auto" 
        :adaptive=true
        :scrollable=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.generate') }} {{ trans('global.certificate.title_singular') }}
                </h3>
                
                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                     </button>
                    <button type="button" class="btn btn-tool"  @click="$modal.hide('certificate-generate-modal')">
                      <i class="fa fa-times"></i>
                    </button>
                 </div>
              
            </div>
            <form >
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group" >
                   <label for="certificate_id" >{{ trans("global.certificate.title_singular") }}</label>

                   <multiselect :options="certificates" 
                                :multiple="false" 
                                :close-on-select="true" 
                                :clear-on-select="false" 
                                :preserve-search="true" 
                                v-model="certificate"
                                placeholder="Pick some" 
                                label="title" 
                                track-by="id" 
                                :preselect-first="true"
                                @input="onChange">
                       <template slot="selection" slot-scope="{ values, search, isOpen }">
                           <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">
                               {{ value.length }} options selected
                           </span>
                       </template>
                   </multiselect>

                </div>
                <div class="form-group " >
                   <label for="title">{{ trans('global.date') }} *</label>
                   <input 
                       type="text" id="date" 
                       name="date" 
                       v-model="date"
                       class="form-control" 
                       placeholder="01.01.2020" 
                       required
                       />
                 </div>
            </div>
                <div class="card-footer">
                     <span class="pull-right">
                         <button type="button" class="btn btn-info" @click="$modal.hide('certificate-generate-modal')">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-primary" @click="generateCertificate()" >{{ trans('global.save') }}</button>
                    </span>
                </div>
            </form>
        </div>
    </modal>
</template>

<script>
    import Multiselect from 'vue-multiselect'
    export default {
        props: {
            'certificates': Array
        },
        data() {
            return {
                method: 'post',
                certificate: null,
                certificate_id: null,
                date: new Date().toLocaleDateString(),
                curriculum_id: null,
                requestUrl: '/certificates/generate',
            }
        },
        methods: {
            async generateCertificate() {
                try {                    
                   await axios.post('/certificates/generate', {
                       'certificate_id': this.certificate_id, 
                       'user_ids' : getDatatablesIds('#users-datatable'), 
                       'date': this.date, 
                       'curriculum_id': this.curriculum_id
                   });
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
            },
            onChange(value){
                this.certificate_id = value.id;
            },
            beforeOpen(event) { 
                if (event.params.curriculum_id){
                    this.curriculum_id =  event.params.curriculum_id;
                }
            },
            
            beforeClose() { 
                //console.log('close') 
            },
            
            submit() {
                var method = this.method.toLowerCase();  
            }
        },
        created() {

        },
        mounted() {
            //console.log('Component mounted.')
        },
        components: {
            Multiselect, 
        },
    }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>