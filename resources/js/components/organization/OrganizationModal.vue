<template>
    <modal
        id="organization-modal"
        name="organization-modal"
        height="auto"
        width="70%"
        :adaptive=true
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

                    {{ trans('global.organization.title_singular') }}
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
                    <label for="title">{{ trans('global.organization.fields.title') }} *</label>
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
                <div class="form-group ">
                    <label for="description">{{ trans('global.organization.fields.description') }}</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="2"
                        class="form-control"
                        v-model="form.description"
                        placeholder="Description"
                    ></textarea>
                    <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
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
                    <p class="help-block" v-if="form.errors.street" v-text="form.errors.street[0]"></p>
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
                    <p class="help-block" v-if="form.errors.postcode" v-text="form.errors.postcode[0]"></p>
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
                    <p class="help-block" v-if="form.errors.city" v-text="form.errors.city[0]"></p>
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
                    <p class="help-block" v-if="form.errors.phone" v-text="form.errors.phone[0]"></p>
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
                    <p class="help-block" v-if="form.errors.email" v-text="form.errors.email[0]"></p>
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
                    <p class="help-block" v-if="form.errors.status" v-text="form.errors.status[0]"></p>
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
                    'title': '',
                    'description': '',
                    'street': '',
                    'postcode': '',
                    'city': '',
                    'phone': '',
                    'email': '',
                    'status': '',
                }),
                statuses: null,
            }
        },
        methods: {
            async submit() {
                try {
                    this.location = (await axios.post('/organizations', this.form)).data.message;
                } catch(error) {
                    this.form.errors = error.response.data.form.errors;
                }
            },
            beforeOpen(event) {
                this.getStatuses();
                if (event.params.id){
                    //console.log(event.params.id)
                    this.load(event.params.id)

                    //this.form.populate( event.params.organization );
                }

                this.method = event.params.method;
            },
            beforeClose() {
                console.log('close')
            },
            async getStatuses() {
                try {
                    console.log('statuses');
                    this.statuses = (await axios.get('/statuses/')).data.message;
                    console.log(this.statuses);
                } catch(error) {
                    console.log('loading failed')
                }
            },
            async load(id) {
                try {
                    this.form.populate((await axios.get('/organizations/'+id)).data.message);
                } catch(error) {
                    console.log('loading failed')
                }
            },

        },
        mounted() {
            console.log('Component mounted.')
        },
    }
</script>
