<template>
    <div>
        <div class="btn-group"
             @click="loadModal()"
        >
            <span v-if="medium_id !== null && (referenceable_type == 'App\\Logbook' || referenceable_type == 'App\\Kanban')"
                class="d-flex align-items-center"
            >
                <a class="text-danger px-2" style="height: 26px;" role="button" @click.stop="removeSubscription()">
                    <i class="fa fa-trash"></i>
                </a>
            </span>
            <button type="button" class="btn btn-default">
                <img  v-if="this.thumbnail_medium_id"
                      alt="preview"
                      :src="'/media/'+this.thumbnail_medium_id"
                      height="50px"
                      class="pull-left"
                />
            </button>
            <a id="btn_add_Medium"
               class="btn btn-primary text-white "
               style="display: flex;
                justify-content: center;
                align-items: center;"
            >
                <i class="fa fa-cloud-upload-alt pr-2"></i>
                {{ trans('global.media.title_singular') }}
            </a>
        </div>
<!--        <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>-->
    </div>
</template>
<script>
export default {
    name: 'MediumForm',
    props: {
        id: '',
        medium_id: {
            default: false,
        },
        accept: {
            default: '',
        },
        form: {},
        referenceable_type: {
            default: null, // null will throw 500, value needs to be set
        },
        referenceable_id: {
            default: null,
        }
    },
    data () {
        return {
            thumbnail_medium_id: '',
            selectedMediumId: '',
        }
    },
    watch: {
        medium_id: function(newVal, oldVal) {
            this.thumbnail_medium_id = newVal;
        }
    },
    mounted () {
        if (this.medium_id) {
            this.thumbnail_medium_id = this.medium_id;
        }
        //set event_listener for thumbnail
        this.$eventHub.$on('addMedia', (e) => {
            if (this.id == e.id) {
                this.thumbnail_medium_id = e.selectedMediumId;
            }
        });
    },
    methods: {
        loadModal() {
            app.__vue__.$modal.show('medium-create-modal',
                {
                    'referenceable_type': this.referenceable_type,
                    'referenceable_id': this.referenceable_id,
                    'description': '',
                    'accept': this.accept,
                    'eventHubCallbackFunction': 'addMedia',
                    'eventHubCallbackFunctionParams': this.id,
                });
        },
        removeSubscription() {
            //! temporary solution to remove media for Logbooks/Kanbans
            this.$parent.removeMedium();
            axios.post('/mediumSubscriptions/destroy', {
                'medium_id': this.medium_id,
                'subscribable_id': this.referenceable_id,
                'subscribable_type': this.referenceable_type,
                // since we don't have those values, only allow default values
                'sharing_level_id': 1,
                'visibility': 1,
            });
        },
    }
}
</script>
