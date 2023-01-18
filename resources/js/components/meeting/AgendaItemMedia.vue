<template>
    <div>
        <div v-for="medium in media"
            class="bg-light">
            <div
                v-bind:id="'medium_'+medium.id"
                class="pull-left ml-2 border pointer"
                style="width: 100px !important; min-height: 60px !important; padding: 0; background-size: cover;"
                :style="{'background-image':'url(/media/' + medium.id + ')'}"
                @click="show('medium', medium)"
            >
                <i v-if="medium.mime_type === 'application/pdf'"
                   class="far fa-file-pdf text-primary text-center  ml-2 px-4 py-2"
                   style="font-size:250%;"></i>
                <i v-if="medium.mime_type === 'url'"
                   class="fa fa-link text-primary text-center ml-2 px-4 py-2"
                   style="font-size:250%;"></i>
            </div>
            <span class="w100 clearfix">
                <span class="m-2 pull-left">{{ medium.title }}</span>
            </span>
            <hr class="my-0">
        </div>
        <div class="bg-light pointer"
             v-if="showAddBtn"
             @click="addMedia()">
            <i class="fas fa-plus m-3 text-muted"></i> {{ trans('global.media.add') }}

        </div>


    </div>
</template>
<script>
import Medium from "../media/Medium";
export default {
    name: 'AgendaItemMedia',
    components: {Medium},
    props: {
        media: [],
        subscription: [],
        showAddBtn:{
            type: Boolean,
            default: true
        },
    },
    data () {
        return {

        };
    },
    methods: {
        show(model, entry) {
            this.$modal.show(model.toLowerCase()+'-modal', { 'content': entry});
        },
        addMedia() {
            this.$modal.show(
                'medium-create-modal',
                {
                    'referenceable_type': 'App\\\AgendaItem',
                    'referenceable_id': this.subscription.id,
                    'eventHubCallbackFunction': 'reload_agenda',
                    'eventHubCallbackFunctionParams': this.subscription.agenda_id,
                });
        },

    },

}
</script>
