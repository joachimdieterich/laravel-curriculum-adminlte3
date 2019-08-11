<template>
    <div>
        
        <ul class="nav nav-pills">
            <li v-for="typetab in typetabs" class="nav-item p-2">
                <a class="nav-link " :href="'#tab_' + typetab" 
                   :class="(activetab == typetab) ? 'active' : ''"
                   @click="setActiveTab(typetab)"
                   data-toggle="tab">
                    {{ getTypeTitle(typetab)[0]['title'] }}
                </a>
            </li>
           
        </ul>   
        
        <div v-for="typetab in typetabs" class="tab-content">
            <div class="tab-pane" :id="'tab_' + typetab"  
                 :class="(activetab == typetab) ? 'active show' : ''">
                 <div class="px-2" v-for="objective in filterTerminalObjectives(typetab)" :id="'terminalObjective_' + objective.id" >
                    <div class="row">
                        <div class="col-12">

                            <ObjectiveBox type="terminal" 
                                          :objective="objective"
                                          :settings="settings">
                            </ObjectiveBox>

                            <div class="ml-auto">
                                <EnablingObjectives 
                                    :curriculum="curriculum"
                                    :terminalobjective="objective"
                                    :objectives="filterEnablingObjectives(objective.id)"
                                    :settings="settings"
                                    >
                                </EnablingObjectives>
                            </div>
                        </div>
                    </div>
                </div>     
            </div><!-- /.tab-pane -->
        </div> <!-- /.tab-content -->
       
        <div class="row" 
             v-if="settings.edit === true">
            <div id="createTerminalRow" class="col-12"> 
            <ObjectiveBox type="createterminal" :objective="{'curriculum_id': curriculum.id}"></ObjectiveBox>
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
            'terminalobjectives': Array, 
            'enablingobjectives': Array, 
            'objectivetypes': Array,   
        }, 
        data() {
            return { 
                settings: {
                    'last': null,
                },
                typetabs: {},
                activetab: null,
                
            }
        },
        methods: {
            filterEnablingObjectives(terminalObjectiveId) {
                let filteredEnablingObjectives = this.enablingobjectives;
                filteredEnablingObjectives = filteredEnablingObjectives.filter(
                    m => m.terminal_objective_id === terminalObjectiveId
                  );
                
                return filteredEnablingObjectives;
            },
            filterTerminalObjectives(typetab) {
                let filteredTerminalObjectives = this.terminalobjectives;
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
            }        
            
        },
        mounted() {
            if (this.terminalobjectives.length != 0){
                this.settings = this.$attrs.settings;
                this.settings.last = this.terminalobjectives[this.terminalobjectives.length-1].id;
               
                this.typetabs = [ ... new Set(this.terminalobjectives.map(t => t.objective_type_id))];
                
                this.activetab = this.typetabs[0];
            }
        },
        
        components: {
            ObjectiveBox, 
            EnablingObjectives
        }
    }
</script>