<template>
    <div>
       
        <div v-for="objective in objectives" :id="'enablingObjective_' + objective.id" style="float:left ">
                <ObjectiveBox type="enabling" :objective="objective" :setting="setting"></ObjectiveBox>
        </div>

        <div id="createEnablingRow" > 
            <ObjectiveBox type="createenabling" 
                :objective="{'curriculum_id': curriculum.id, 'terminal_objective_id': terminalobjective.id}">
            </ObjectiveBox>
        </div>
     
    </div>
</template>

<script>
    import ObjectiveBox from './ObjectiveBox'
    
    export default {
        props: {'objectives': Array, 
                'curriculum': Object,
                'terminalobjective': Object
        }, 
         data() {
            return {
                setting: {
                    'last': null,
                },
            }
        },
        mounted() {

            if (this.objectives.length != 0){
                this.setting.last = this.objectives[this.objectives.length-1].id;
            }
            
        },
        components: {
            ObjectiveBox
        }
    }
</script>