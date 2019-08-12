<template>
    <modal 
        id="group-modal" 
        name="group-modal" 
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
                <h3 class="card-title">{{ trans('global.create') }} {{ trans('global.group.title_singular') }}</h3>
                <div class="card-tools">
                   <button type="button" class="btn btn-tool" data-widget="remove" @click="$emit('close')">
                     <i class="fa fa-times"></i>
                   </button>
                 </div>
              
            </div>
            <form >
            <div class="card-body">
                <div class="form-group "
                    :class="errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.group.fields.title') }} *</label>
                    <input 
                        type="text" id="title" 
                        name="title" 
                        class="form-control" 
                        v-model="form.title"
                        placeholder="Title" 
                        required
                        />
                     <p class="help-block" v-if="errors.title" v-text="errors.title[0]"></p>
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

    export default {
        data() {
            return {
                form: {
                    'title': '',
                },
                errors: {}
            }
        },
        methods: {
            async submit() {
                try {
                    this.location = (await axios.post('/groups', this.form)).data.message;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
