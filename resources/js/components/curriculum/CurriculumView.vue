<template>
    <div class="col-12">

            <aside class="control-sidebar control-sidebar-light">
                <!-- Control sidebar content goes here -->
                <media subscribable_type="App\Curriculum"
                       :subscribable_id="curriculum.id"
                       format="list"></media>
            </aside>

       <TerminalObjectives
           :curriculum="cur"
           :objectivetypes="objectivetypes"
           :settings="settings">
       </TerminalObjectives>
    </div>
</template>

<script>
    import TerminalObjectives from '../objectives/TerminalObjectives.vue'
    import Media from '../media/Media';

    export default {
        props: {
            'curriculum': Object,
//           'terminalobjectives': Array,
//           'enablingobjectives': Array,
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
            this.$on('addTerminalObjective', function(newTerminalObjective) {
                //console.log(newTerminalObjective);
             });
        },
        components: {
            TerminalObjectives,
            Media
        }

    }
</script>
