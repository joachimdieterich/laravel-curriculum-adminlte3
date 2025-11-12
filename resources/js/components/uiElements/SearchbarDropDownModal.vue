<template>
    <DropDownModal
        :show="show"
        :title="trans('global.search.modal.title')"
        :show-title="false"
    >
        <template v-slot:body>
            <Select2
                id="tag-search"
                name="tag-search"
                url="/tags"
                :additional_query_param="this.typeParameter()"
                model="tags"
                :label="trans('global.tag.title')"
                :multiple="true"
                :selected="selectedTags"
                @selectedValue="(idArray) => {selectedTagsBuffer = idArray; this.$emit('tagSelectionChange', idArray);}"
                @cleared="() => {selectedTagsBuffer = []; this.$emit('tagSelectionChange', []);}"
            />
        </template>
    </DropDownModal>
</template>

<script>
import DropDownModal from "./DropDownModal.vue";
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global.js";

export default {
    name: 'SearchbarDropDownModal',
    components: {Select2, DropDownModal},
    setup () {
        const globalStore = useGlobalStore();

        return {
            globalStore,
        };
    },
    data() {
        return {
            searchTagModelContext: null,
            selectedTags: [],
            selectedTagsBuffer: [],
        }
    },
    props: {
        show: {
            type: Boolean,
            required: true
        },
    },
    methods: {
        typeParameter() {
            let searchTagModelContext = this.globalStore['searchTagModelContext'];
            if (searchTagModelContext === null) {
                console.error('No searchTagModelContext is defined in the global store.')
            }

            return {
                'type': searchTagModelContext,
            };
        },
    },
    watch: {
        'globalStore.searchTagModelContext': function (newValue) {
            this.searchTagModelContext = newValue;
        },
        show: function () {
            this.selectedTags = this.selectedTagsBuffer;
        },
    }
}
</script>