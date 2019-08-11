<template>
    <div>
        <div v-for="curriculum in curricula_list">
            <span class="col-xs-12" >
                <h4 class="text-black bg-light p-2" 
                    data-toggle="collapse" 
                    :data-target="'#'+tagName(curriculum.id)" 
                    aria-expanded="true">
                    {{curriculum.title}}
                    <small>
                        {{curriculum.organization_type.title}}
                    </small>
                    <button class="btn btn-box-tool float-right" 
                            style="padding-top:0;" 
                            type="button" 
                            title="Fach einklappen bzw. ausklappen">
                        <i class="fa fa-expand float-right"></i>
                    </button>
                </h4>
            </span>
            <div class="collapse" 
                 :id="tagName(curriculum.id)">
                <div v-for="filtered_reference in filterReferences(curriculum.id)" >
                    <div class="row">
                        <div class="col-xs-12 col-sm-3 pull-left" >
                            <dt>Thema/Kompetenzbereich</dt>

                            <ObjectiveBox type="terminal" 
                                          :objective="(filtered_reference.referenceable_type == 'App\\TerminalObjective') ? filtered_reference.referenceable : filtered_reference.referenceable.terminal_objective"
                                          :setting="setting">                   
                        </ObjectiveBox>
                        </div>

                        <div class="col-xs-12 col-sm-3" v-if="filtered_reference.referenceable_type === 'App\\EnablingObjective'">
                            <dt>Kompetenz</dt>
                            <ObjectiveBox type="enabling" 
                                          :objective="filtered_reference.referenceable"
                                          :setting="setting">
                            </ObjectiveBox>
                        </div>

                        <div class="col-xs-12 col-sm-6 pull-right">
                            <dt>Anregungen zur Unterrichtsgestaltung </dt>
                            <dd v-html="filtered_reference.reference.description"></dd>
                        </div>
                    </div>
                    <hr style="clear:both;">
                </div>
            </div>    
        </div>
    </div>
</template>


<script>
    import ObjectiveBox from '../objectives/ObjectiveBox';
    
    export default {
        props: ['reference_subscriptions','curricula_list'],
        data: function() {
            return {
              setting: {
                    'last': null,  
                },
               
            }
        },
        methods: {
            filterReferences(curriculum_id) {
                
                let filteredReferences = this.reference_subscriptions;
                filteredReferences = filteredReferences.filter(
                    c => c.referenceable.curriculum_id === curriculum_id
                  );
                return filteredReferences;
            },
            filterCurriculum(curriculum_id) {
                let curricula = this.curriculua_list;
                curricula = curricula.filter(
                    c => c.id === curriculum_id
                  );
                
                return curricula;
            },
            tagName: function(i){
                return 'curriculum_'+i;
            }
      
        }, 
       
        components: {
           ObjectiveBox,
        },
       
        }
</script>
