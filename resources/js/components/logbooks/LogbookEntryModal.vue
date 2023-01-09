<template>
    <modal
        id="logbook-entry-modal"
        name="logbook-entry-modal"
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
                    <span v-if="method === 'post'">
                        {{ trans('global.logbookEntry.create')  }}
                    </span>

                    <span v-if="method === 'patch'">
                        {{ trans('global.logbookEntry.edit')  }}
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

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.logbook.fields.title') }} *</label>
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
                    <label for="description">{{ trans('global.logbook.fields.description') }}</label>
                    <textarea
                    id="description"
                    name="description"
                    class="form-control description my-editor "
                    v-model="form.description"
                    ></textarea>
                    <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                </div>
                <date-picker class="w-100"
                    v-model="time"
                    type="datetime" range
                    valueType="YYYY-MM-DD HH:mm:ss"></date-picker>
            </div>
            <div class="card-footer">
                <span class="pull-right">
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';

    export default {
        data() {
            return {
                method: 'post',
                requestUrl: '/logbookEntries',
                categories: {},
                time: null,
                form: new Form({
                    'id':'',
                    'logbook_id':'',
                    'title': '',
                    'description': '',
                    'begin': '',
                    'end': ''
                })
            };
        },
        methods: {
            async submit() {
                try {
                    this.form.description = tinyMCE.get('description').getContent();
                    this.form.begin = this.time[0];
                    this.form.end = this.time[1];
                    if (this.method === 'patch') {
                        this.new_entry = (await axios.patch('/logbookEntries/' + this.form.id, this.form)).data.message;
                        this.$eventHub.$emit('updateLogbookEntry', this.new_entry);
                    } else {
                        this.new_entry = (await axios.post('/logbookEntries', this.form)).data.message;
                        this.$eventHub.$emit('addLogbookEntry', this.new_entry);
                    }

                    this.close();
                } catch(error) {
                    this.form.errors = error.response.data.form.errors;
                }
            },
            beforeOpen(event) {
                this.form.clear();
                if (event.params.id){
                    this.load(event.params.id);
                }
                if (event.params.logbook_id){
                    this.form.logbook_id = event.params.logbook_id;
                }
                this.method = event.params.method;
                this.time = [moment().format("YYYY-MM-DD HH:mm:ss"), moment().add(30, 'minutes').format("YYYY-MM-DD HH:mm:ss")];
            },

            opened(){
                this.$initTinyMCE();
            },

            beforeClose() {},

            async load(id) {
                try {
                    this.form.populate((await axios.get('/logbookEntries/'+id)).data.message);
                    this.time = [this.form.begin, this.form.end];
                } catch(error) {
                    //console.log('loading failed')
                }
            },
            close(){
                //console.log('close')
                this.$modal.hide('logbook-entry-modal');
            }
        },
        mounted() {
        },
        components: {
            DatePicker
        },
    }
</script>

