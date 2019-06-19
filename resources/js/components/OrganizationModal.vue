<template>
    <modal 
        id="organization-modal" 
        name="organization-modal" 
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
                <h3 class="card-title">{{ trans('global.create') }} {{ trans('global.organization.title_singular') }}</h3>
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
                    <label for="title">{{ trans('global.organization.fields.title') }} *</label>
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
                <div class="form-group ">
                    <label for="description">{{ trans('global.organization.fields.description') }}</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="2"
                        class="form-control" 
                        v-model="form.description"
                        placeholder="Description" 
                        >form.description</textarea>
                    <p class="help-block" v-if="errors.description" v-text="errors.description[0]"></p>
                </div>
                <div class="form-group ">
                    <label for="street">{{ trans('global.organization.fields.street') }}</label>
                    <input 
                        type="text" id="street" 
                        name="street" 
                        class="form-control" 
                        v-model="form.street"
                        placeholder="Street" 
                        />
                    <p class="help-block" v-if="errors.street" v-text="errors.street[0]"></p>
                </div>
                <div class="form-group ">
                    <label for="postcode">{{ trans('global.organization.fields.postcode') }}</label>
                    <input 
                        type="text" id="postcode" 
                        name="postcode" 
                        class="form-control" 
                        v-model="form.postcode"
                        placeholder="Postcode" 
                        />
                    <p class="help-block" v-if="errors.postcode" v-text="errors.postcode[0]"></p>
                </div>
                <div class="form-group ">
                    <label for="city">{{ trans('global.organization.fields.city') }}</label>
                    <input 
                        type="text" id="city" 
                        name="city" 
                        class="form-control" 
                        v-model="form.city"
                        placeholder="City" 
                        />
                    <p class="help-block" v-if="errors.city" v-text="errors.city[0]"></p>
                </div>
                <div class="form-group ">
                    <label for="phone">{{ trans('global.organization.fields.phone') }}</label>
                    <input 
                        type="text" id="phone" 
                        name="phone" 
                        class="form-control" 
                        v-model="form.phone"
                        placeholder="Phone" 
                        />
                    <p class="help-block" v-if="errors.phone" v-text="errors.phone[0]"></p>
                </div>
                <div class="form-group ">
                    <label for="email">{{ trans('global.organization.fields.email') }}</label>
                    <input 
                        type="text" id="email" 
                        name="phone" 
                        class="form-control" 
                        v-model="form.email"
                        placeholder="Email" 
                        />
                    <p class="help-block" v-if="errors.email" v-text="errors.email[0]"></p>
                </div>
                <div class="form-group ">
                    <label for="status">{{ trans('global.organization.fields.status') }}</label>
                    <input 
                        type="text" id="status" 
                        name="phone" 
                        class="form-control" 
                        v-model="form.status"
                        placeholder="Status" 
                        readonly
                        />
                    <p class="help-block" v-if="errors.status" v-text="errors.status[0]"></p>
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
                    'description': '',
                    'street': '',
                    'postcode': '',
                    'city': '',
                    'phone': '',
                    'email': '',
                    'status': '',
                },
                errors: {}
            }
        },
        methods: {
            async submit() {
                try {
                    this.location = (await axios.post('/admin/organizations', this.form)).data.message;
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
