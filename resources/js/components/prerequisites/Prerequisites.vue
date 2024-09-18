<template>
    <div>
        <div v-if="prerequisites.model"
             class="row" >
            <div class="col-4">
                <h4>{{trans('global.prerequisite.title')}}
                    <i class="pull-right fa fa-2 fa-arrow-right"></i>
                </h4>
                <div v-for="item in this.prerequisites.children">
                    <ObjectiveBox
                        v-if="item.model.predecessor_type ==='App\\TerminalObjective'"
                       :objective="item.model.predecessor"
                       :settings="settings"
                       type="terminal">
                    </ObjectiveBox>
                    <ObjectiveBox
                        v-else-if="item.model.predecessor_type ==='App\\EnablingObjective'"
                       :objective="item.model.predecessor"
                       :settings="settings"
                       type="enabling">
                    </ObjectiveBox>
                </div>

                <hr class="clearfix">
                <ul>
                    <li v-for="item in this.prerequisites.children"
                        v-can="'prerequisite_delete'">
                        {{ item.name }}
                        <br>
                        <small>{{ item.description }}</small>
                        <i class="fa fa-trash text-danger pull-right pointer"
                           @click="destroyPrerequisite(item.prerequisite_id)"></i>
                    </li>
                </ul>
            </div>

            <div class="col-4">
                <h4>
                    <span>&nbsp;</span>
                    <i class="pull-right fa fa-2 fa-arrow-right"></i>
                </h4>
                <div class="d-flex justify-content-center">
                    <ObjectiveBox
                        class="clearfix"
                        :objective="prerequisites.model"
                        :settings="settings"
                        type="terminal">
                    </ObjectiveBox>
                </div>

            </div>

            <div class="col-4">
                <h4 class="pull-right">
                    Anschlussfähiges Wissen
                </h4>
                <div v-for="item in this.prerequisites.parents"
                    class="clearfix pull-right">
                    <objective-box v-if="item.model.successor_type ==='App\\TerminalObjective'"
                                   :objective="item.model.successor"
                                   :settings="settings"
                                   type="terminal">
                    </objective-box>
                    <objective-box v-else-if="item.model.successor_type ==='App\\EnablingObjective'"
                                   :objective="item.model.successor"
                                   :settings="settings"
                                   type="enabling">
                    </objective-box>
                </div>
            </div>
        </div>

<!--        <section id="visualisation" ></section>-->
    </div>

</template>
<script>
import ObjectiveBox from "../objectives/ObjectiveBox.vue";
    export default {
        name: 'prerequisites',
        props: {
            successor_type: String,
            successor_id: Number,
        },
        data() {
            return {
                prerequisites : {},
                settings: {
                    'edit': false,
                },
                errors: {},
            }
        },
        methods: {
            loaderEvent() {
                axios.get('/prerequisites?successor_type=' + this.successor_type + '&successor_id=' + this.successor_id)
                    .then(response => {
                        this.processResponse(response);
                    }).catch(e => {
                    this.errors = e.response.data.errors;
                });
            },
            destroyPrerequisite(id){
                axios.delete("/prerequisites/" + id )
                    .then(res => {
                        this.loaderEvent();
                    }).catch(e => {
                    console.log(e);
                });
            },
            processResponse(response){
                this.prerequisites = response.data.prerequisites;
            },
            removeHtmlTags(string){
               return string.replace(/(<([^>]+)>)/ig,"");
            }
        },
        mounted() {
        },
        components: {
            ObjectiveBox
        },
    }
</script>
