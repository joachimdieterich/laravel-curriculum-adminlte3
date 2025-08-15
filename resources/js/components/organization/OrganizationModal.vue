<template>
    <modal
        id="organization-modal"
        name="organization-modal"
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
                    <span v-if="method === 'post'">
                        {{ trans('global.organization.create') }}
                    </span>

                    <span v-if="method === 'patch'">
                        {{ trans('global.organization.edit') }}
                    </span>
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
            <form >
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
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
                        class="form-control description my-editor "
                        v-model="form.description"
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
                        name="email"
                        class="form-control"
                        v-model="form.email"
                        placeholder="Email"
                        />
                    <p class="help-block" v-if="form.errors.email" v-text="form.errors.email[0]"></p>
                </div>
                <div class="form-group ">
                    <label>{{ trans('global.organization.fields.status') }}</label>
                    <select class="form-control"
                            v-model="form.status_definition_id"
                            id="status"
                            name="status"
                            >
                        <option v-for="(item,index) in status_definitions" v-bind:value="item.status_definition_id">
                            {{ item.lang_de }}
                        </option>
                    </select>
                    <p class="help-block" v-if="form.errors.status_definition_id" v-text="form.errors.status_definition_id[0]"></p>
                </div>

            </div>
                <div class="card-footer">
                     <span class="pull-right">
                         <button id="organization-cancel"
                                 type="button"
                                 class="btn btn-default"
                                 data-widget="remove" @click="close()">{{ trans('global.cancel') }}</button>
                         <button id="organization-save"
                                 class="btn btn-primary" @click="submit(method)" >{{ trans('global.save') }}</button>
                    </span>
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
                component_id: this._uid,
                method: 'post',
                requestUrl: '/enablingObjectives',
                form: new Form({
                    'id':'',
                    'title': '',
                    'description': '',
                    'street': '',
                    'postcode': '',
                    'city': '',
                    'phone': '',
                    'email': '',
                    'status_definition_id': '',
                }),
                status_definitions: null,
            }
        },
        methods: {
            async submit(method) {
                try {
                    this.form.description = tinyMCE.get('description').getContent();
                    if (method === 'patch'){
                        this.location = (await axios.patch('/organizations/'+this.form.id, this.form)).data.message;
                    } else {
                        this.location = (await axios.post('/organizations', this.form)).data.message;
                    }

                } catch(error) {
                    this.form.errors = error.response.data.form.errors;
                }
            },
            beforeOpen(event) {
                this.getStatusDefinitions();
                if (event.params.id){
                    this.load(event.params.id)
                }

                this.method = event.params.method;
            },
            beforeClose(event) {

            },
            async getStatusDefinitions() {
                try {
                    this.status_definitions = (await axios.get('/statusdefinitions/')).data.message;
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
            opened(){
                this.$initTinyMCE(
                    [
                        "autolink link example"
                    ],
                    {
                        'eventHubCallbackFunction': 'insertContent',
                        'eventHubCallbackFunctionParams': this.component_id,
                    }
                );
            },
            close(){
                this.$modal.hide('organization-modal');
            }

        },
        mounted() {
            console.log('Component mounted.')
        },
    }
</script>
