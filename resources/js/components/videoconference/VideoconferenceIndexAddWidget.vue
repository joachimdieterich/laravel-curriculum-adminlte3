<template>
    <div>
        <div
            v-if="visible"
            :id="'videoconference-add'"
            class="box box-objective nav-item-box-image pointer my-1"
            style="min-width: 200px !important; border-style: dotted !important; "
            @click="open()">
            <div class="nav-item-box-image-size text-center">
                <i class="fa fa-2x p-5 fa-plus nav-item-text text-muted"></i>
            </div>

            <span class="text-center p-1 overflow-auto nav-item-box bg-gray-light">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ trans('global.videoconference.create') }}
                   </h1>
            </span>
        </div>
        <!-- Create Modal -->
        <videoconferenceCreate
            id="modal-videoconference-form"
            :method="method"
            :videoconference="videoconference"
        />
    </div>

</template>
<script>
import Form from "form-backend-validation";
import videoconferenceCreate from "./VideoconferenceCreate";

export default {
    name: 'videoconferenceIndexAddWidget',
    props: {
        visible: {
            type: Boolean,
            default: true
        },

    },
    data() {
        return {
            videoconference: null,
            method: {
                type: String,
                default: 'post'
            }
        }
    },
    methods: {
       open(method = 'post'){
           this.method = method;
           $('#modal-videoconference-form').modal('show');
       },
    },
    mounted() {
        this.$eventHub.$on('edit_videoconference', (videoconference) => {
            this.videoconference = videoconference;
            this.open('patch');
        });
    },
    components: {
        videoconferenceCreate,
    },
}
</script>
