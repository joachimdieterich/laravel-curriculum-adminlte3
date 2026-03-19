<template>
    <div>
        <div v-for="curriculum in curricula_list"
            class="card mb-2"
        >
            <div class="card-header">
                <div
                    class="w-100 pointer"
                    data-card-widget="collapse"
                    :data-target="'#curriculum_' + curriculum.id"
                >
                    <span class="h4 mr-2">{{ curriculum.title }}</span>
                    <small>{{ curriculum.organization_type.title }}</small>
                    <span class="pull-right">
                        <i class="fas fa-expand-alt"></i>
                    </span>
                </div>
            </div>
            <div
                :id="'curriculum_' + curriculum.id"
                class="card-body"
            >
                <div v-for="filtered_reference in filterReferences(curriculum.id)">
                    <div class="objectives">
                        <ObjectiveBox
                            type="terminal"
                            :objective="(filtered_reference.referenceable_type == 'App\\TerminalObjective') ? filtered_reference.referenceable : filtered_reference.referenceable.terminal_objective"
                            :setting="setting"
                        />

                        <ObjectiveBox v-if="filtered_reference.referenceable_type === 'App\\EnablingObjective'"
                            type="enabling"
                            :objective="filtered_reference.referenceable"
                            :setting="setting"
                        />

                        <div>
                            <div class="d-flex align-items-center">
                                <strong>{{ trans("global.curricula_cross_references_description") }}</strong>
                                <button
                                    v-permission="'reference_edit'"
                                    class="btn btn-icon ml-2"
                                    @click.prevent="open(filtered_reference.reference)"
                                >
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                            </div>
                            <div v-html="filtered_reference.reference.description"></div>
                        </div>
                    </div>
                    <hr style="clear:both;">
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ObjectiveBox from '../objectives/ObjectiveBox.vue';
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        objective: {
            type: Object,
        },
        type: {
            type: String,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data: function() {
        return {
            reference_subscriptions: [],
            curricula_list: [],
            setting: {
                last: null,
            },
        }
    },
    methods: {
        loaderEvent() {
            axios.get('/' + this.type + 'Objectives/' + this.objective.id + '/referenceSubscriptionSiblings')
                .then(response => {
                    if (response.data.siblings.length !== 0) {
                        this.reference_subscriptions = response.data.siblings;
                        this.curricula_list = response.data.curricula_list;
                    }
                })
                .catch(e => {
                    console.log(e);
                });
        },
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
        open(reference) {
            this.globalStore?.showModal('reference-objective-modal', {
                id: reference.id,
                description: reference.description,
                url: '/references',
            });
        },
    },
    components: {
        ObjectiveBox,
    },
}
</script>