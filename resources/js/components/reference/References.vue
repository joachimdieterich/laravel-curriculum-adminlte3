<template>
    <div>
        <div class="card collapsed-card mb-2" v-for="curriculum in curricula_list">
            <div class="card-header">
                <h3 class="card-title"
                    data-card-widget="collapse"
                    :data-target="'#'+tagName(curriculum.id)"
                    aria-expanded="true">
                    {{curriculum.title}}
                    <small>
                        {{curriculum.organization_type.title}}
                    </small>
                </h3>
                <div class="card-tools pull-right">
                    <button class="btn btn-tool"
                            data-card-widget="collapse"
                            :data-target="'#'+tagName(curriculum.id)">
                        <i class="fas fa-expand-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body collapse"
                 :id="tagName(curriculum.id)">
                <div v-for="filtered_reference in filterReferences(curriculum.id)" >
                    <div class="row pl-3">
                        <ObjectiveBox type="terminal"
                            :objective="(filtered_reference.referenceable_type == 'App\\TerminalObjective') ? filtered_reference.referenceable : filtered_reference.referenceable.terminal_objective"
                            :setting="setting">
                        </ObjectiveBox>

                        <ObjectiveBox
                            v-if="filtered_reference.referenceable_type === 'App\\EnablingObjective'"
                            type="enabling"
                            :objective="filtered_reference.referenceable"
                            :setting="setting">
                        </ObjectiveBox>

                        <div>
                            <dt>
                                {{ trans("global.curricula_cross_references_description") }}
                                <a v-can="'reference_edit'" class="pull-right pr-2 pointer"
                                   @click.prevent="open('reference-objective-modal', filtered_reference.reference)">
                                    <i class="fa fa-pencil-alt pl-2"></i></a>
                             </dt>
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
            },
            open(modal, reference){
                this.$modal.show(modal, {'id': reference.id, 'description': reference.description});
            },

        },

        components: {
           ObjectiveBox,
        },

        }
</script>
