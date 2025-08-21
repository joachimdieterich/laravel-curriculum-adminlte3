<template >

    <div
        v-bind:id="'medium_'+medium.id"
        class="box box-objective pointer my-1"
        style="height: 300px !important; min-width: 200px !important; padding: 0; background-size: 100%,50%;"
        :style="{'background-image':'url('+href+')'}"
        @click="show('medium', medium)"
    >
        <div class="symbol"
             style="position: absolute;
                padding: 6px;
                z-index: 1;
                width: 30px;
                height: 40px;
                background-color: #0583C9;
                top: 0px;
                font-size: 1.2em;
                left: 10px;">

            <i v-if="medium.mime_type === 'application/pdf'" class="fa fa-file-pdf text-white pt-2"></i>
            <i v-if="medium.mime_type === 'url'" class="fa fa-link text-white pt-2"></i>
            <i v-else class="fa fa-photo-video text-white pt-2"></i>
        </div>

        <i v-if="medium.mime_type === 'application/pdf'" class="far fa-file-pdf text-primary text-center pt-2"
           style="position:absolute; top: 0px; height: 150px !important; width: 100%; font-size:800%;"></i>
        <i v-if="medium.mime_type === 'url'" class="fa fa-link text-primary text-center pt-2"
           style="position:absolute; top: 0px; height: 150px !important; width: 100%; font-size:800%;"></i>
        <span
            v-permission="'medium_delete'"
            class="p-1 pointer_hand"
            accesskey=""
            style="position:absolute; top:0px; height: 30px; width:100%;"
        >
                <button
                    :id="'delete-medium' + medium.id"
                    type="submit"
                    class="btn btn-danger btn-sm pull-right"
                    @click.stop="unlinkMedium();"
                >
                    <small>
                        <i class="fa fa-unlink"></i>
                    </small>
                </button>
        </span>
        <span
            class="bg-white text-center p-1 overflow-auto"
            style="position :absolute; bottom: 0px; height: 150px; width: 100%;"
        >
            <h6 class="events-heading pt-1 hyphens" v-html="medium.title"></h6>
            <p class=" text-muted small" v-html="medium.description"></p>
       </span>
    </div>
</template>
<script>
export default {
    props: {
        subscription: {},
        medium: {},
    },
    methods: {
        show(model, entry) {
            this.$modal.show(model.toLowerCase()+'-modal', { 'content': entry});
        },
        async unlinkMedium() { //id of external reference and value in db
            try {
                await axios.post('/mediumSubscriptions/destroy', this.subscription).data;
            } catch(error) {
                console.log(error.response.data.errors);
            }
            $("#medium_" + this.medium.id).hide();
        },
    },
    computed: {
        href: function () {
            return '/media/' + this.subscription.medium_id;
        },
    },
    bevorOpen() {
            axios.get('/media/' + this.subscription.medium_id).then(response => {
            this.sharingLevels = response.data.sharingLevel;
        }).catch(e => {
            console.log(e);
        });
    },
}
</script>