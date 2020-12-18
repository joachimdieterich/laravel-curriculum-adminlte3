<template>
    <div>

        <ul class="nav nav-pills"
            id="terminalObjectivesTopNav">
            <li v-for="typetab in typetabs" class="nav-item pl-0 pr-2 pb-2 pt-2">
                <a class="nav-link " :href="'#tab_' + typetab"
                   :class="(activetab == typetab) ? 'active' : ''"
                   @click="setActiveTab(typetab)"
                   data-toggle="tab">
                    {{ getTypeTitle(typetab)[0]['title'] }}
                </a>
            </li>
            <div class="form-group pt-2 ml-auto">
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
            </div>
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
                                :settings="settings">
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
            <ObjectiveBox type="createterminal"
                          :objective="{'curriculum_id': curriculum.id}"
                          :settings="settings"
                          ></ObjectiveBox>
            </div>
        </div>

    </div>
</template>

<script>
    import ObjectiveBox from './ObjectiveBox'
    import EnablingObjectives from './EnablingObjectives'

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
                typetabs: {},
                activetab: null,
                currentCurriculaEnrolments: null,
                errors: {}
            }
        },
        methods: {
            filterTerminalObjectives(typetab) {
                let filteredTerminalObjectives = this.curriculum.terminal_objectives;
                filteredTerminalObjectives = filteredTerminalObjectives.filter(
                    t => t.objective_type_id === typetab
                  );

                return filteredTerminalObjectives;
            },
            getTypeTitle(id){

                var typeObject = this.objectivetypes.filter(
                    t => t.id === id
                  );

                return typeObject;
            },
            setActiveTab(typetab){
                this.activetab = typetab;
            },

        },
        mounted() {
            this.settings = this.$attrs.settings;

            if (this.curriculum.terminal_objectives.length != 0){
                this.settings.last = this.curriculum.terminal_objectives[this.curriculum.terminal_objectives.length-1].id;

                this.typetabs = [ ... new Set(this.curriculum.terminal_objectives.map(t => t.objective_type_id))];

                this.activetab = this.typetabs[0];
            }
            //load users curricula for cross reference selector
            axios.get('/curricula/references')
                .then(response => {
                    this.currentCurriculaEnrolments = response.data.message;
                })
                .catch(e => {
                    this.errors = error.response.data.errors;
                });
            this.$nextTick(() => {
                $("#currentCurriculaEnrolmentSelector").select2({
                    placeholder: "Querverweise",
                    allowClear: true
                }).on('select2:select', function (e) {
                    this.$parent.setCrossReferenceCurriculumId($("#currentCurriculaEnrolmentSelector").val());
                }.bind(this));
            })


        },

        components: {
            ObjectiveBox,
            EnablingObjectives
        }
    }
</script>
