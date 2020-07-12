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
                    {{ trans('global.certificate.generate') }}
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
                 <div class="form-group ">
                    <label for="level_id">
                        {{ trans("global.certificate.title_singular") }}
                    </label>
                    <select name="certificates" 
                            id="certificates" 
                            class="form-control select2 "
                            style="width:100%;"
                            >
                        <option v-for="(item,index) in certificates" v-bind:value="item.id">{{ item.title }}</option>
                    </select>     
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
                         <button class="btn btn-primary" @click="generateCertificate()" >{{ trans('global.generate') }}</button>
                    </span>
                </div>
            </form>
        </div>
    </modal>
</template>

<script>
    
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
                location: null
            }
        },
        methods: {
            async generateCertificate() {
                try {                    
                   this.location = (await axios.post('/certificates/generate', {
                       'certificate_id': this.certificate_id, 
                       'user_ids' : getDatatablesIds('#users-datatable'), 
                       'date': this.date, 
                       'curriculum_id': this.curriculum_id
                   })).data.message;
                } catch(error) {
                    this.errors = error.response.data.errors;
                } 
                window.location = this.location;
            },
            onChange(value){
                this.certificate_id = value.id;
            },
            beforeOpen(event) { 
                if (event.params.curriculum_id){
                    this.curriculum_id =  event.params.curriculum_id;
                }
            },
            opened(){
                this.initSelect2(); 
            },
            initSelect2(){
                $("#certificates").select2({
                    dropdownParent: $("#certificates").parent(),
                    allowClear: false
                }).on('select2:select', function (e) { 
                    this.onChange(e.params.data);
                }.bind(this)) //make onChange accessible! 
                 .val(certificates[0].id).trigger('change'); //set default
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
        }
    }
</script>
