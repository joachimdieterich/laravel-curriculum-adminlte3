<template>
    <div>
        <div
            class="btn-group"
            @click="loadModal()"
        >
            <div
                id="loading-overlay"
                class="overlay"
                style="background-color: #fff8 !important; display: none;"
                @click.stop
            >
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            </div>
            <span v-if="medium_id !== null"
                class="d-flex align-items-center"
            >
                <a
                    class="text-danger px-2"
                    style="height: 26px;"
                    role="button"
                    @click.stop="removeSubscription()"
                >
                    <i class="fa fa-trash"></i>
                </a>
            </span>
            <button
                type="button"
                class="btn btn-default"
            >
                <img v-if="thumbnail_medium_id"
                    alt="preview"
                    :src="'/media/' + thumbnail_medium_id + '?preview=true'"
                    height="50px"
                    class="pull-left"
                />
            </button>
            <a
                id="btn_add_Medium"
                class="btn btn-primary d-flex justify-content-center align-items-center text-white"
            >
                <i class="fa fa-cloud-upload-alt pr-2"></i>
                {{ trans('global.medium.title_singular') }}
            </a>
        </div>
    </div>
</template>
<script>
import {useGlobalStore} from "../../store/global";
import {useToast} from "vue-toastification";

export default {
    name: 'MediumForm',
    props: {
        medium_id: {
            type: Number,
            default: null,
        },
        accept: {
            type: String,
            default: '',
        },
        subscribable_type: {
            type: String,
            default: null,
        },
        subscribable_id: {
            type: Number,
            default: null,
        }
    },
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            showMediumModal: false,
            thumbnail_medium_id: '',
            selectedMediumId: '',
        }
    },
    watch: {
        medium_id: function(newVal, oldVal) {
            this.thumbnail_medium_id = newVal;
        },
    },
    mounted() {
        if (this.medium_id) {
            this.thumbnail_medium_id = this.medium_id;
        }
        //set event_listener for thumbnail
        this.$eventHub.on('medium-added', (data) => {
            if (this.component_id == data.id) {
                this.showMediumModal = false;
                this.$emit("selectedValue", data.selectedMedia[0].id ?? data.selectedMedia[0].medium_id); // TODO: select all files
            }
        });
    },
    methods: {
        loadModal() {
            this.globalStore?.showModal('medium-modal', {
                subscribable_id: this.subscribable_id,
                subscribable_type: this.subscribable_type,
                subscribeSelected: false,
                accept: this.accept,
                public: this.public,
                callbackId: this.component_id,
            });
        },
        removeSubscription() {
            $('#loading-overlay').show();
            axios.post('/mediumSubscriptions/destroy', {
                medium_id: this.medium_id,
                subscribable_id: this.subscribable_id,
                subscribable_type: this.subscribable_type,
                // since we don't have those values, only allow default values
                sharing_level_id: 1,
                visibility: 1,
            })
            .then(response => {
                $('#loading-overlay').hide();
                this.$emit("selectedValue", null);
            })
            .catch(e => {
                this.toast.error(this.errorMessage(e));
                $('#loading-overlay').hide();
                console.log(e);
            });
        },
    },
}
</script>
<style scoped>
.loading.show {
    opacity: 0.5;
}
</style>