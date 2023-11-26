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
             style="margin-bottom: 0 !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ trans('global.referenceable_types.objective') }}
                 </h3>
                 <div class="card-tools">
                     <button v-permission="'objective_delete'"
                             v-if="method !== 'post'"
                             type="button"
                             class="btn btn-tool"
                             @click="del()">
                         <i class="fa fa-trash text-danger"></i>
                     </button>
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
                    <label for="curricula">
                        {{ trans('global.curriculum.title_singular') }}
                    </label>
                    <select name="curricula"
                            id="curricula"
                            v-model="form.curriculum_id"
                            class="form-control select2 "
                            style="width:100%;"
                            >
                         <option v-for="item in curricula" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>
                <div v-if="method === 'post'" class="form-group ">
                    <label for="terminalObjectives">
                        {{ trans('global.terminalObjective.title_singular') }}
                    </label>
                    <select name="terminalObjectives"
                            id="terminalObjectives"
                            class="form-control select2 "
                            style="width:100%;"
                            >
                         <option v-for="item in terminalObjectives" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>
                <div v-if="method === 'post'" class="form-group ">
                    <label for="enablingObjectives">
                        {{ trans('global.enablingObjective.title_singular') }}
                    </label>
                    <select name="enablingObjectives"
                            id="enablingObjectives"
                            class="form-control select2 "
                            style="width:100%;">
                         <option v-for="item in enablingObjectives" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
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
<!--                     <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>-->
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';
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
                requestUrl: '',
            }
        },
        methods: {

            async loadCurricula() {
                try {
                    this.curricula = (await axios.get('/curricula')).data.curricula;

                } catch(error) {
                    console.log(error);
                }
                this.form.terminal_objective_id = null; //reset selection
                this.form.enabling_objective_id = null;
            },

            async loadObjectives(id) {
                this.form.curriculum_id = parseInt(id)
                try {
                   this.terminalObjectives = (await axios.get('/curricula/'+this.form.curriculum_id+'/objectives')).data.curriculum.terminal_objectives;
                   this.removeHtmlTags(this.terminalObjectives);
                   this.form.terminal_objective_id = this.terminalObjectives[0].id;
                   this.loadEnabling(this.form.terminal_objective_id);
                } catch(error) {
                   this.errors = error.response.data.errors;
                }
            },
            loadEnabling(id){
                let terminal = [].concat(...this.terminalObjectives.filter(ena => ena.enabling_objectives.find(e => e.terminal_objective_id === parseInt(id))));
                this.enablingObjectives = terminal[0].enabling_objectives;
                this.removeHtmlTags(this.enablingObjectives);
                this.form.terminal_objective_id = parseInt(id);
                this.form.enabling_objective_id = terminal[0].enabling_objectives[0].id;
                this.requestUrl = this.referenceRequestUrl ? this.referenceRequestUrl : '/terminalObjectiveSubscriptions';
                this.initSelect2();
            },
            setEnabling(id){
                this.form.enabling_objective_id = parseInt(id);
                this.requestUrl = this.referenceRequestUrl ? this.referenceRequestUrl : '/enablingObjectiveSubscriptions';
                this.initSelect2();
            },
            async submit() {
                try {
                    if (this.method === 'patch'){
                        this.location = (await axios.patch('/references/'+this.form.id, {
                            'description' : tinyMCE.get('description').getContent(),
                        })).data.message;
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
                this.form.curriculum_id = null;
                this.curricula = {};
                this.terminalObjectives = {};
                this.enablingObjectives = {};
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
                this.$initTinyMCE([
                    "autolink link example"
                ]);
                this.initSelect2();
            },
            initSelect2(){
                $("#curricula").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false,
                }).on('select2:select', function (e) {
                    this.loadObjectives(e.params.data.id);
                    this.terminalObjectives = {};
                    this.enablingObjectives = {};
                }.bind(this)) //make loadObjectives accessible!
                .val(this.form.curriculum_id).trigger('change'); //set value

                $("#terminalObjectives").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.loadEnabling(e.params.data.id);
                }.bind(this)) //make loadEnabling accessible!
                .val(this.form.terminal_objective_id).trigger('change'); //set value

                $("#enablingObjectives").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: true
                }).on('select2:select', function (e) {
                    this.setEnabling(e.params.data.id);
                }.bind(this)) //make setEnabling accessible!
               .val(this.form.enabling_objective_id).trigger('change'); //set value

            },
            close(){
                this.$modal.hide('reference-objective-modal');
            },
            removeHtmlTags(array){
                var i;
                for (i = 0; i < array.length; i++) {
                    array[i].title = array[i].title.replace(/(<([^>]+)>)/ig,"");
                }
            },
            async del(){
                try {
                    await axios.delete('/references/'+this.form.id);
                }
                catch(error) {
                    this.errors = response.data.errors;
                }
                location.reload();
            },
        }
    }
</script>

