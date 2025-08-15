<template>
    <modal
        id="prerequisite-modal"
        name="prerequisite-modal"
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
                    {{ trans('global.prerequisite.title') }}
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

                <div v-if="method === 'post'" class="form-group ">
                    <label for="curricula">
                        {{ trans('global.curriculum.title_singular') }}
                    </label>
                    <select name="curricula"
                            id="curricula"
                            class="form-control select2 "
                            style="width:100%;">
                         <option></option>
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
                            style="width:100%;">
                         <option></option>
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
                         <option></option>
                         <option v-for="item in enablingObjectives" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>
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
    export default {
        data() {
            return {
                method: 'post',
                form: new Form({
                    'id': null,
                    'successor_type': null,
                    'successor_id': null,
                    'curriculum_id': null,
                    'terminal_objective_id': null,
                    'enabling_objective_id': null,
                }),

                curricula: {},
                curriculum: {},
                terminalObjectives: {},
                terminalObjective: {},
                enablingObjectives: {},
                enablingObjective: {},
                requestUrl: '/prerequisites',
            }
        },
        methods: {

            async loadCurricula() {
                try {
                    this.curricula = (await axios.get('/curricula')).data.curricula;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
                this.form.terminal_objective_id = null; //reset selection
                this.form.enabling_objective_id = null;
            },

            async loadObjectives(id) {
                this.form.curriculum_id = parseInt(id)
                try {
                   this.terminalObjectives = (await axios.get('/curricula/'+this.form.curriculum_id+'/objectives')).data.curriculum.terminal_objectives;
                   this.removeHtmlTags(this.terminalObjectives);
                   this.form.terminal_objective_id = null;
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
                this.initSelect2();
            },
            setEnabling(id){
                this.form.enabling_objective_id = parseInt(id);
                this.initSelect2();
            },
            async submit() {
                try {
                    if (this.method === 'patch'){
                        this.location = (await axios.patch(this.requestUrl + '/' + this.form.id, )).data.message;
                    } else {
                        this.location = (await axios.post(this.requestUrl, {
                            'curriculum_id':         this.form.curriculum_id,
                            'terminal_objective_id': this.form.terminal_objective_id,
                            'enabling_objective_id': this.form.enabling_objective_id,
                            'successor_type':        this.form.successor_type,
                            'successor_id':          this.form.successor_id,
                        })).data.message;
                    }
                    location.reload(true);
                } catch(error) {
                    //
                }
            },

            beforeOpen(event) {
                this.loadCurricula();
                if (event.params.referenceable_type){
                    this.form.successor_type = event.params.referenceable_type;
                    this.form.successor_id   = event.params.referenceable_id;
                }
             },
            beforeClose() {
            },
            opened(){
                this.initSelect2();
                this.curricula = {};
                this.terminalObjectives = {};
                this.enablingObjectives = {};
            },
            initSelect2(){
                $("#curricula").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false
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
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.setEnabling(e.params.data.id);
                }.bind(this)) //make setEnabling accessible!
               .val(this.form.enabling_objective_id).trigger('change'); //set value
            },
            close(){
                this.$modal.hide('prerequisite-modal');
            },
            removeHtmlTags(array){
                var i;
                for (i = 0; i < array.length; i++) {
                    array[i].title = array[i].title.replace(/(<([^>]+)>)/ig,"");
                }
            }
        }
    }
</script>

