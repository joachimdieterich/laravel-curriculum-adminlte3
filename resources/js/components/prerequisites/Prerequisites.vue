<template>
    <div>
        <div v-if="prerequisites.model"
            class="row"
        >
            <div class="col-4">
                <h4>
                    {{trans('global.prerequisite.title')}}
                    <i class="pull-right fa fa-2 fa-arrow-right"></i>
                </h4>
                <div v-for="item in prerequisites.children">
                    <ObjectiveBox v-if="item.model.predecessor_type === 'App\\TerminalObjective'"
                        type="terminal"
                        :objective="item.model.predecessor"
                        :settings="settings"
                    />
                    <ObjectiveBox v-else-if="item.model.predecessor_type === 'App\\EnablingObjective'"
                        type="enabling"
                        :objective="item.model.predecessor"
                        :settings="settings"
                    />
                </div>
                <hr class="clearfix">
                <ul>
                    <li v-for="item in prerequisites.children"
                        v-permission="'prerequisite_delete'"
                    >
                        {{ item.name }}
                        <br>
                        <small>{{ item.description }}</small>
                        <i
                            class="fa fa-trash text-danger pull-right pointer"
                            @click="destroyPrerequisite(item.prerequisite_id)"
                        ></i>
                    </li>
                </ul>
            </div>

            <div class="col-4">
                <h4>
                    <span>&nbsp;</span>
                    <i class="pull-right fa fa-2 fa-arrow-right"></i>
                </h4>
                <div class="objectives d-flex justify-content-center">
                    <ObjectiveBox
                        type="terminal"
                        class="clearfix"
                        :objective="prerequisites.model"
                        :settings="settings"
                    />
                </div>
            </div>

            <div class="col-4">
                <h4 class="pull-right">
                    Anschlussfähiges Wissen
                </h4>
                <div v-for="item in prerequisites.parents"
                    class="clearfix pull-right"
                >
                    <ObjectiveBox v-if="item.model.successor_type === 'App\\TerminalObjective'"
                        type="terminal"
                        :objective="item.model.successor"
                        :settings="settings"
                    />
                    <ObjectiveBox v-else-if="item.model.successor_type === 'App\\EnablingObjective'"
                        type="enabling"
                        :objective="item.model.successor"
                        :settings="settings"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ObjectiveBox from "../objectives/ObjectiveBox.vue";

export default {
    name: 'prerequisites',
    props: {
        successor_type: {
            type: String,
            default: null,
        },
        successor_id: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            prerequisites : {},
            settings: {
                edit: false,
            },
        }
    },
    methods: {
        loaderEvent() {
            axios.get('/prerequisites?successor_type=' + this.successor_type + '&successor_id=' + this.successor_id)
                .then(response => {
                    this.processResponse(response);
                }).catch(e => {
                    console.log(e);
                });
        },
        destroyPrerequisite(id) {
            axios.delete("/prerequisites/" + id )
                .then(res => {
                    this.loaderEvent();
                }).catch(e => {
                    console.log(e);
                });
        },
        processResponse(response) {
            this.prerequisites = response.data.prerequisites;
        },
        removeHtmlTags(string) {
            return string.replace(/(<([^>]+)>)/ig,"");
        },
    },
    components: {
        ObjectiveBox,
    },
}
</script>