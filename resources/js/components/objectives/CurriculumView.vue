<template>
    <div class="col-12">
       <TerminalObjectives
           :curriculum="cur"
           :objectivetypes="objectivetypes"
           :settings="settings">
       </TerminalObjectives>
    </div>
</template>

<script>
    import TerminalObjectives from '../objectives/TerminalObjectives.vue'

    export default {
        props: {
            'curriculum': Object,
            'objectivetypes': Array,
            'settings': Object,
        },
        data () {
            return {
                cur: this.curriculum
            };
        },

        methods: {
            externalEvent: function(ids) {
               this.reloadEnablingObjectives(ids);
            },
            async reloadEnablingObjectives(ids) {
                try {
                    this.cur = (await axios.post('/curricula/'+this.curriculum.id+'/achievements', {'user_ids' : ids})).data.curriculum;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            setCrossReferenceCurriculumId: function(curriculum_id) { //can be called external
                this.settings.cross_reference_curriculum_id = curriculum_id;
            }
        },
        mounted() {

        },
        components: {
            TerminalObjectives
        }

    }
</script>
