<template >
    <div class="boxfooter row" v-bind:style="{ 'color': textcolor }">
        <div class="p-0 col-12 boxflex">
            <span v-if="withmedia" >
                <span class="fa fa-briefcase mr-1"
                    @click.prevent="showModal('objective-medium-modal')">
                </span>
            </span>
            <span v-else >
                <span class="fa fa-briefcase mr-1 text-gray" style="cursor: not-allowed">
                </span>
            </span>

             <AchievementIndicator v-can="'achievement_create'"
                :objective="objective"
                :type="type"
                :settings="settings">
             </AchievementIndicator>

            <span >  
                <span class="fa fa-info ml-1 "
                    @click.prevent="showDetails()">
                </span>
            </span>
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
        computed: {
             withmedia: function () {
                if ((typeof this.objective.media_subscriptions !== 'undefined') || (typeof this.objective.reference_subscriptions !== 'undefined')){
                     if (this.objective.media_subscriptions.length > 0 || this.objective.reference_subscriptions.length > 0){
                         return true;
                     }
                }
                return false;
            },
        },
        components: {
            AchievementIndicator, 
        },
    }
</script>
