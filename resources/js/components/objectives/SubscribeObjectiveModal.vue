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
                            class="form-control select2 "
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
                            class="form-control select2 "
                            style="width:100%;"
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
                            class="form-control select2 "
                            style="width:100%;"
                            >
                         <option v-for="(item,index) in enablingObjectives" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>

            </div>
            <div class="card-footer">
                <span class="pull-right">
                     <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
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
                terminal_objective_id: null,
                enablingObjectives: {},
                enablingObjective: {},
                enabling_objective_id: null,
                requestUrl: null
            };
        },
        methods: {
            async loadCurricula() {
                try {
                    this.curricula = (await axios.get('/curricula')).data.curricula;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
                this.terminal_objective_id = null; //reset selection
                this.enabling_objective_id = null;
            },

            async loadObjectives(id) {
                this.curriculum_id = parseInt(id);
                try {
                   this.terminalObjectives = (await axios.get('/curricula/'+this.curriculum_id+'/objectives')).data.curriculum.terminal_objectives;
                   this.removeHtmlTags(this.terminalObjectives);
                   this.terminal_objective_id = this.terminalObjectives[0].id;
                   this.loadEnabling(this.terminal_objective_id);
                   this.initSelect2();
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            loadEnabling(id){
                let terminal = [].concat(...this.terminalObjectives.filter(ena => ena.enabling_objectives.find(e => e.terminal_objective_id === parseInt(id))));
                this.enablingObjectives = terminal[0].enabling_objectives;
                this.removeHtmlTags(this.enablingObjectives);
                this.terminal_objective_id = parseInt(id);
                this.enabling_objective_id = terminal[0].enabling_objectives[0].id;
                this.requestUrl = '/terminalObjectiveSubscriptions';
                this.initSelect2();
            },
            setEnabling(id){
                this.enabling_objective_id = parseInt(id);
                this.requestUrl = '/enablingObjectiveSubscriptions';
                this.initSelect2();
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
                this.loadCurricula();
                if (event.params.referenceable_type){
                    this.referenceable_type = event.params.referenceable_type;
                    this.referenceable_id = event.params.referenceable_id;
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
                }.bind(this)); //make loadObjectives accessible!

                $("#terminalObjectives").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false
                }).on('select2:select', function (e) {
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
                var i;
                for (i = 0; i < array.length; i++) {
                    array[i].title = array[i].title.replace(/<[^>]+>/g, '');
                }
            }

        }
    }
</script>

