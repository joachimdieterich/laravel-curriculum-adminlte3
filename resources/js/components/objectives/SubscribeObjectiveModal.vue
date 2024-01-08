<template>
    <modal
        id="subscribe-objective-modal"
        name="subscribe-objective-modal"
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
                    {{ trans('global.referenceable_types.objective') }}
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

                <div class="form-group ">
                    <label for="curricula">
                        {{ trans('global.curriculum.title_singular') }}
                    </label>
                    <select name="curricula"
                            id="curricula"
                            v-model="curriculum_id"
                            class="form-control select2"
                            style="width:100%;"
                    >
                        <option v-for="(item,index) in curricula" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>

                <div class="form-group ">
                    <label for="terminalObjectives">
                        {{ trans('global.terminalObjective.title_singular') }}
                    </label>
                    <select name="terminalObjectives"
                            id="terminalObjectives"
                            v-model="terminal_objective_id"
                            class="form-control select2"
                            style="width:100%;"
                            multiple="multiple"
                            :disabled="isObjEmpty(terminalObjectives) ? 'disabled' : null"
                    >
                        <option v-for="(item,index) in terminalObjectives" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>

                <div class="form-group ">
                    <label for="enablingObjectives">
                        {{ trans('global.enablingObjective.title_singular') }}
                    </label>
                    <select name="enablingObjectives"
                            id="enablingObjectives"
                            v-model="enabling_objective_id"
                            class="form-control select2"
                            style="width:100%;"
                            multiple="multiple"
                            :disabled="isObjEmpty(enablingObjectives) ? 'disabled' : null"
                    >
                        <option v-for="(item,index) in enablingObjectives" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>

            </div>
            <div class="card-footer">
                <span class="d-flex justify-content-between">
                     <button type="button" class="btn btn-default" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    export default {
        data() {
            return {
                referenceable_type: null,
                referenceable_id: null,
                curricula: {},
                curriculum: {},
                curriculum_id: null,
                terminalObjectives: {},
                terminalObjective: {},
                terminal_objective_id: [],
                enablingObjectives: {},
                enablingObjective: {},
                enabling_objective_id: [],
                requestUrl: null
            };
        },
        mounted() {
            this.loadCurricula();
        },
        methods: {
            async loadCurricula() {
                try {
                    this.curricula = (await axios.get('/curricula')).data.curricula;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            async loadObjectives(id) {
                this.curriculum_id = parseInt(id);
                try {
                    this.terminalObjectives = (await axios.get('/curricula/'+this.curriculum_id+'/objectives')).data.curriculum.terminal_objectives;
                    this.removeHtmlTags(this.terminalObjectives);
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            loadEnabling(id){
                this.terminal_objective_id.push(id);

                if (this.terminal_objective_id.length > 1) return;

                this.enablingObjectives = this.terminalObjectives.find(terminal => terminal.id === parseInt(id)).enabling_objectives;
                this.removeHtmlTags(this.enablingObjectives);
                this.requestUrl = '/terminalObjectiveSubscriptions';
                
                // needs to be cleared, else the selected option will appear on other objectives
                $("#enablingObjectives").val(null).trigger('change');
                // this select2 needs to be reinitialized, else it won't update the select-options
                // the 'on:select' is still functionable
                $("#enablingObjectives").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false
                });
            },
            setEnabling(id){
                this.enabling_objective_id.push(id);
                this.requestUrl = '/enablingObjectiveSubscriptions';
            },
            async submit() {
                try {
                    this.location = (await axios.post(this.requestUrl, {
                        'curriculum_id':            this.curriculum_id,
                        'terminal_objective_id':    this.terminal_objective_id,
                        'enabling_objective_id':    this.enabling_objective_id,
                        'subscribable_type':        this.referenceable_type,
                        'subscribable_id':          this.referenceable_id
                    })).data.message;
                    location.reload(true);

                } catch(error) {
                    //
                }
            },
            beforeOpen(event) {
                if (event.params.referenceable_type){
                    this.referenceable_type = event.params.referenceable_type;
                    this.referenceable_id = event.params.referenceable_id;
                }
            },
            beforeClose() {},
            opened() {
                this.initSelect2();
            },
            initSelect2(){
                $("#curricula").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.terminal_objective_id = [];
                    this.terminalObjectives = {};
                    this.enablingObjectives = {};
                    this.loadObjectives(e.params.data.id);
                }.bind(this)); //make loadObjectives accessible!

                $("#terminalObjectives").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.enabling_objective_id = [];
                    this.enablingObjectives = {};
                    this.loadEnabling(e.params.data.id);
                }.bind(this)); //make loadEnabling accessible!

                $("#enablingObjectives").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.setEnabling(e.params.data.id);
                }.bind(this)); //make setEnabling accessible!

            },
            close(){
                this.$modal.hide('subscribe-objective-modal');
            },
            removeHtmlTags(array, field){
                for (let i = 0; i < array.length; i++) {
                    array[i].title = array[i].title.replace(/(<([^>]+)>)/ig, "");
                }
            },
            isObjEmpty(obj) { // needs wrapper function, since jQuery can't be referenced in HTML
                return $.isEmptyObject(obj);
            },
        }
    }
</script>

