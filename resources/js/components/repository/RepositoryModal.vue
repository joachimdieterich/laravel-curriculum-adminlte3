<template>
    <modal
        id="repository-modal"
        name="repository-modal"
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
                    {{ trans('global.externalRepositorySubscription.create') }}
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
                <div class="form-group"
                    :class="form.errors.title ? 'has-error' : ''">
                   <label for="objective_type_id" >{{ trans("global.objectiveType.title_singular") }}</label>

                   <multiselect v-model="endpoint"
                                :options="enpoints"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
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
                   <p class="help-block" v-if="form.errors.objective_type_id" v-text="form.errors.objective_type_id[0]"></p>

               </div>
                <div class="form-group ">
                    <label for="title">{{ trans('global.task.fields.title') }}</label>
                    <input
                        type="text" id="title"
                        name="title"
                        class="form-control"
                        v-model="form.title"
                        placeholder="title"
                        />
                    <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                </div>

                <div class="form-group ">
                    <label for="description">{{ trans('global.task.fields.description') }}</label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control description my-editor "
                        v-model="form.description"
                    ></textarea>
                    <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                </div>

                <div class="form-group ">
                    <label for="start_date">{{ trans('global.task.fields.start_date') }}</label>
                    <date-picker
                        class="w-100"
                        v-model="form.start_date"
                        type="datetime"
                        valueType="YYYY-MM-DD HH:mm:ss">
                    </date-picker>
                    <p class="help-block" v-if="form.errors.start_date" v-text="form.errors.start_date[0]"></p>
                </div>

                <div class="form-group ">
                    <label for="due_date">{{ trans('global.task.fields.due_date') }}</label>
                    <date-picker
                        class="w-100"
                        v-model="form.due_date"
                        type="datetime"
                        valueType="YYYY-MM-DD HH:mm:ss">
                    </date-picker>
                    <p class="help-block" v-if="form.errors.due_date" v-text="form.errors.due_date[0]"></p>
                </div>

            </div>

            <div class="card-footer">
                <span class="pull-right">
                     <button type="button" class="btn btn-default" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
import Form from 'form-backend-validation';
const DatePicker =
    () => import('vue2-datepicker');

/*
    import DatePicker from 'vue2-datepicker';
*/
    import 'vue2-datepicker/index.css';
    export default {
        data() {
            return {
                method: 'post',
                requestUrl: '/tasks',

                form: new Form({
                    'id': '',
                    'title': '',
                    'description': '',
                    'start_date': '',
                    'due_date': '',
                    'subscribable_type': null,
                    'subscribable_id': null,
                }),
            }
        },
        methods: {
            async load(id) {
                try {
                    this.form.populate((await axios.get('/tasks/'+id)).data.message);
                } catch(error) {
                    //console.log('loading failed')
                }
            },

            submit() {
                var method = this.method.toLowerCase();
                this.form.description = tinyMCE.get('description').getContent();

                if (method === 'patch') {
                    this.requestUrl += '/' + this.form.id;
                }

                this.form.submit(method, this.requestUrl);

                this.$modal.hide('repository-modal');
                location.reload(true);
                //todo .then .catch
            },

            beforeOpen(event) {
                if  (event.params.id){
                    this.method = event.params.method;
                    this.load(event.params.id);

                } else if(event.params.subscribable_type){
                    this.form.subscribable_type = event.params.subscribable_type;
                    this.form.subscribable_id = event.params.subscribable_id;
                }
             },
            opened(){
                this.$initTinyMCE();
            },
            beforeClose(event) {

            },

            close(){
                this.$modal.hide('repository-modal');
            },


        },
        components: {
            DatePicker
        },
    }
</script>

