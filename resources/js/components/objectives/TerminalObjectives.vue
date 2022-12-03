<template>
    <div>
        <ul class="nav nav-pills"
            id="terminalObjectivesTopNav">
            <draggable
                class="nav nav-pills"
                v-can="'curriculum_edit'"
                v-model="typetabs"
                @start="drag=true"
                @end="handleTypeMoved">
                <li v-for="typetab in typetabs"
                    class="nav-item pl-0 pr-2 pb-2 pt-2">
                    <a class="nav-link " :href="'#tab_' + typetab"
                       :class="(activetab == typetab) ? 'active' : ''"
                       @click="setActiveTab(typetab)"
                       data-toggle="tab">
                        {{ getTypeTitle(typetab)[0]['title'] }}
                    </a>
                </li>
            </draggable>
            <li v-hide-if-permission="'curriculum_edit'"
                v-for="typetab in typetabs"
                class="nav-item pl-0 pr-2 pb-2 pt-2">
                <a class="nav-link " :href="'#tab_' + typetab"
                   :class="(activetab == typetab) ? 'active' : ''"
                   @click="setActiveTab(typetab)"
                   data-toggle="tab">
                    {{ getTypeTitle(typetab)[0]['title'] }}
                </a>
            </li>

            <li class="form-group pt-2 ml-auto">
                <select
                    name="currentCurriculaEnrolmentSelector"
                    id="currentCurriculaEnrolmentSelector"
                    class="form-control select2 "
                    style="width:100%;"
                    >
                    <option
                        v-for="(item,index) in currentCurriculaEnrolments"
                        :value="item.id">
                        {{ item.title }}
                    </option>
                </select>
            </li>
        </ul>
        <hr class="mt-0">
        <div v-for="typetab in typetabs" class="tab-content">
            <div class="tab-pane" :id="'tab_' + typetab"
                 :class="(activetab == typetab) ? 'active show' : ''">
                 <div v-for="objective in filterTerminalObjectives(typetab)" :id="'terminalObjective_' + objective.id" >
                    <div class="row">
                        <div class="col-12 terminal-row">

                            <ObjectiveBox
                                type="terminal"
                                :objective="objective"
                                :settings="settings"
                                :max_id="max_ids[activetab]">
                            </ObjectiveBox>

                            <div class="ml-auto">
                                <EnablingObjectives
                                    :curriculum="curriculum"
                                    :terminalobjective="objective"
                                    :objectives="objective.enabling_objectives"
                                    :settings="settings">
                                </EnablingObjectives>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.tab-pane -->
        </div> <!-- /.tab-content -->

        <div class="row"
             v-can="'curriculum_edit'"
             v-if="settings.edit === true">
            <div id="createTerminalRow" class="col-12">
            <ObjectiveBox
                type="createterminal"
                :objective="{'curriculum_id': curriculum.id}"
                :objective_type_id="activetab"
                :settings="settings"
                :max_id="max_ids[activetab]"
              ></ObjectiveBox>
            </div>
        </div>
        <terminal-objective-modal></terminal-objective-modal>
        <enabling-objective-modal></enabling-objective-modal>
    </div>
</template>

<script>
    import ObjectiveBox from './ObjectiveBox'
    import EnablingObjectives from './EnablingObjectives'
    import draggable from "vuedraggable"; // import the vuedraggable

    export default {
        props: {
            'curriculum': Object,
            'objectivetypes': Array,
        },
        data() {
            return {
                settings: {
                    'last': null,
                },
                max_ids: {},
                typetabs: {},
                activetab: null,
                currentCurriculaEnrolments: null,
                errors: {},

                terminal_objectives: Object
            }
        },
        methods: {
            filterTerminalObjectives(typetab) {
                let filteredTerminalObjectives = this.terminal_objectives;
                filteredTerminalObjectives = filteredTerminalObjectives.filter(
                    t => t.objective_type_id === typetab
                  );
                this.max_ids[typetab] = filteredTerminalObjectives[filteredTerminalObjectives.length-1].id;
                return filteredTerminalObjectives;
            },
            getTypeTitle(id){
                return this.objectivetypes.filter(
                    t => t.id === id
                );
            },
            setActiveTab(typetab){
                this.activetab = typetab;
            },
            loadObjectives(objective_type_id = 0){
                axios.get('/curricula/' + this.curriculum.id + '/objectives' )
                    .then(response => {
                        this.terminal_objectives = response.data.curriculum.terminal_objectives;
                        if (this.terminal_objectives.length !== 0){
                            this.settings.last = this.terminal_objectives[this.terminal_objectives.length-1].id;
                            this.typetabs = [ ... new Set(this.terminal_objectives.map(t => t.objective_type_id))];
                            if (!!this.curriculum.objective_type_order){
                                if (this.curriculum.objective_type_order.length ===  this.typetabs.length){
                                    this.typetabs = this.curriculum.objective_type_order;
                                }
                            }

                            if (objective_type_id === 0){
                                this.activetab = this.typetabs[0];
                            }
                        }
                    })
                    .catch(e => {
                        this.errors = e.data.errors;
                    });
            },
            externalEvent: function(ids) {
                this.reloadEnablingObjectives(ids);
            },
            async reloadEnablingObjectives(ids) {
                try {
                    this.terminal_objectives = (await axios.post('/curricula/'+this.curriculum.id+'/achievements', {'user_ids' : ids})).data.curriculum.terminal_objectives;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },

            checkCrossReferenceInLocalStorage(){
                if (localStorage.getItem( 'currentCurriculaEnrolmentSelectorValue' ) !== null){
                    $("#currentCurriculaEnrolmentSelector").val(localStorage.getItem( 'currentCurriculaEnrolmentSelectorValue' )).trigger('change');
                    this.$parent.setCrossReferenceCurriculumId($("#currentCurriculaEnrolmentSelector").val());
                } else {
                    $("#currentCurriculaEnrolmentSelector").val(null).trigger('change');
                }
            },
            handleTypeMoved() {
                // Send the entire list of statuses to the server
                axios.put("/curricula/"+this.curriculum.id+"/syncObjectiveTypesOrder", {objective_type_order: this.typetabs})
                    .catch(err => {
                        console.log(err.response);
                        alert(err.response.statusText);
                    });
            },
        },
        mounted() {
            this.settings = this.$attrs.settings;
            this.loadObjectives();

            //load users curricula for cross reference selector
            axios.get('/curricula/references')
                .then(response => {
                    this.currentCurriculaEnrolments = response.data.message;
                    this.$nextTick(() => {
                        $("#currentCurriculaEnrolmentSelector").select2({
                            value: null,
                            placeholder: "Querverweise",
                            allowClear: true
                        }).on('select2:select', function () {
                            localStorage.setItem( 'currentCurriculaEnrolmentSelectorValue', $("#currentCurriculaEnrolmentSelector").val() );
                            this.$parent.setCrossReferenceCurriculumId($("#currentCurriculaEnrolmentSelector").val());
                        }.bind(this))
                        .on('select2:clear', function () {
                            this.$parent.setCrossReferenceCurriculumId(false);
                            localStorage.removeItem( 'currentCurriculaEnrolmentSelectorValue');
                        }.bind(this));
                       this.checkCrossReferenceInLocalStorage(); // load value from localStorage
                    })
                })
                .catch(e => {
                    this.errors = e.data.errors;
                });

            //eventlistener
            this.$on('addTerminalObjective', function(newTerminalObjective) {
                this.activetab = newTerminalObjective.objective_type_id;
                this.loadObjectives(this.activetab);
            });
            this.$on('addEnablingObjective', function(newEnablingObjective) {
                this.loadObjectives(this.activetab);
            });
        },

        components: {
            ObjectiveBox,
            EnablingObjectives,
            draggable
        }
    }
</script>
