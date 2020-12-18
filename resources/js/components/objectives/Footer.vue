<template >
    <div class="boxfooter row" v-bind:style="{ 'color': textcolor }">
        <div class="p-0 col-12 ">
            <!--<span v-if="withmedia" >
                <span class="fa fa-briefcase mr-1"
                    @click.prevent="showDetails()">
                </span>
            </span>
            <span v-else >
                <span class="fa fa-briefcase mr-1 text-gray" style="cursor: not-allowed">
                </span>
            </span>-->

             <AchievementIndicator
                 v-can="'achievement_create'"
                 v-if="(typeof this.objective.achievements !== 'undefined')"
                :objective="objective"
                :type="type"
                :settings="settings">
             </AchievementIndicator>

        </div>
    </div>
</template>


<script>
    import AchievementIndicator from './AchievementIndicator'
    export default {
        props: {
                objective: {},
                textcolor: { default: '#000' },
                type:{},
                settings:{}
            },
        methods: {
             showModal(modal) {
                this.$modal.show(modal, {'content': this.objective, 'type': this.type});
            },
            showDetails(modal) {
                location.href= '/'+this.type+'Objectives/'+this.objective.id;
            },
        },
        /*computed: {
             withmedia: function () {
                if (typeof this.objective.media_subscriptions !== 'undefined') {
                     if (this.objective.media_subscriptions.length > 0 ){
                         return true;
                     }
                }
                if (typeof this.objective.referencing_curriculum_id !== "undefined"){
                     if ( this.objective.referencing_curriculum_id !== null){
                         return true;
                     }
                }
                return false;
            },
        },*/
        components: {
            AchievementIndicator,
        },
    }
</script>
