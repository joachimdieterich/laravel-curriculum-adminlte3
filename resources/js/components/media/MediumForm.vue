<template>
    <div>
        <div class="btn-group"
             @click="loadModal()">
            <button type="button" class="btn btn-default ">
                <img  v-if="this.thumbnail_medium_id"
                      alt="preview"
                      :src="'/media/'+this.thumbnail_medium_id"
                      height="50px"
                      class="pull-left">
            </button>
            <a id="btn_add_Medium"
               class="btn btn-primary text-white "
               style="display: flex;
                    justify-content: center;
                    align-items: center;">
                <i class="fa fa-cloud-upload-alt pr-2"></i>
                {{ trans('global.medium.title_singular') }}
            </a>
        </div>
        <Teleport to="body">
            <MediumModal
                :show="this.mediumStore.getShowMediumModal"
                @close="this.mediumStore.setShowMediumModal(false)"
            ></MediumModal>

        </Teleport>
    </div>
</template>
<script>
import MediumModal from "../media/MediumModal.vue";
import {useMediumStore} from "../../store/media";
export default {
  name: 'MediumForm',
    components:{
        MediumModal,
    },
  props: {
      id: '',
      medium_id:{
          default: false
      },
      accept:{
          default: ''
      },
  },
    data () {
        return {
            component_id: this.$.uid,
            showMediumModal: false,
            thumbnail_medium_id: '',
            selectedMediumId: ''
        }
    },
    setup () { //use database store
        const mediumStore = useMediumStore();
        return {
            mediumStore,
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
        this.$eventHub.on('medium-added', (e) => {
            console.log(e);
            if (this.component_id == e.id) {
                this.thumbnail_medium_id = e.selectedMedia[0].id;
                this.showMediumModal = false;
                this.$emit("selectedValue", e.selectedMedia[0].id);
            }
        });
    },
    methods: {
        loadModal() {
            this.mediumStore.setMediumModalParams(
                {
                    'show': true,
                    'subscribeSelected': false,
                    'public': this.public,
                    'callbackId': this.component_id
                });
        }
    }
}
</script>
