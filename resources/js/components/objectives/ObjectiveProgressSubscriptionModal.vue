<template>
    <modal
        id="objective-progress-subscription-modal"
        name="objective-progress-subscription-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 2000">
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
                    <select name="terminalObjectives[]"
                            id="terminalObjectives"
                            class="form-control select2 "
                            style="width:100%;"
                            multiple=true
                            >
                         <option v-for="(item,index) in terminalObjectives" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>

                <div class="form-group ">
                    <label for="enablingObjectives">
                        {{ trans('global.enablingObjective.title_singular') }}
                    </label>
                    <select name="enablingObjective"
                            id="enablingObjectives"
                            class="form-control select2 "
                            style="width:100%;"
                            multiple=true
                            >
                         <option v-for="(item,index) in enablingObjectives" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>

                <div class="form-group ">
                    <label for="title">{{ trans('global.percent_accomplished') }} *</label>
                    <input
                        type="number" id="percentage"
                        name="percentage"
                        class="form-control"
                        v-model="percentage"
                        required
                    />
                </div>

            </div>
            <div class="card-footer">
                <span class="pull-right">
                     <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                     <button class="btn btn-primary" @click.prevent="set()" >{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    export default {

        data() {
            return {
                referenceable_type: "App\\Curriculum",
                referenceable_id: null,
                curricula: {},
                curriculum: {},
                curriculum_id: null,
                terminalObjectives: {},
                terminal_objective_id: null,
                enablingObjectives: {},
                enabling_objective_id: null,
                percentage: 60,
                targetId: null
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
                this.referenceable_id = this.curriculum_id;
                try {
                   this.terminalObjectives = (await axios.get('/curricula/'+this.curriculum_id+'/objectives')).data.curriculum.terminal_objectives;
                   this.removeHtmlTags(this.terminalObjectives);
                   this.terminal_objective_id = this.terminalObjectives[0].id;
                   this.loadEnabling(this.terminal_objective_id, "App\\Curriculum");
                   this.initSelect2();
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            loadEnabling(id, referenceable_type){
                let terminal = [].concat(...this.terminalObjectives.filter(ena => ena.enabling_objectives.find(e => e.terminal_objective_id === parseInt(id))));
                this.enablingObjectives = terminal[0].enabling_objectives;
                this.removeHtmlTags(this.enablingObjectives);
                this.referenceable_id = parseInt(id);
                this.enabling_objective_id = terminal[0].enabling_objectives[0].id;
                this.referenceable_type    = referenceable_type,
                this.initSelect2();
            },
            setEnabling(ids){
                this.referenceable_id = ids;
                this.referenceable_type    = "App\\EnablingObjective",
                this.initSelect2();
            },
            set() {
                $('#progress_reference').val(JSON.stringify({'referenceable_type': this.referenceable_type, 'referenceable_id': this.referenceable_id , 'percentage': this.percentage }));
                $('#progress_reference').trigger("change");
                this.close();
            },

            beforeOpen(event) {
                this.loadCurricula();
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
                    dropdownParent: $("#curricula").parent(),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.loadObjectives(e.params.data.id);
                    this.terminalObjectives = {};
                    this.enablingObjectives = {};
                }.bind(this)); //make loadObjectives accessible!

                $("#terminalObjectives").select2({
                    dropdownParent: $("#terminalObjectives").parent(),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.referenceable_id = $("#terminalObjectives").val();
                    if(this.referenceable_id.length == 1){
                        this.loadEnabling(e.params.data.id, "App\\TerminalObjective");
                    } else {
                        this.enablingObjectives = {};
                    }

                }.bind(this)); //make loadEnabling accessible!

                $("#enablingObjectives").select2({
                    dropdownParent: $("#enablingObjectives").parent(),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.setEnabling($("#enablingObjectives").val(), "App\\EnablingObjective");
                }.bind(this)); //make setEnabling accessible!

            },
            close(){
                this.$modal.hide('objective-progress-subscription-modal');
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

