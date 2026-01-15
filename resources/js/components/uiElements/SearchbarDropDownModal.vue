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
                :label="trans('global.tag.filter.title')"
                :multiple="true"
                :selected="selectedTags"
                @selectedValue="(idArray) => {selectedTagsBuffer = idArray; this.$emit('tagSelectionChange', idArray);}"
                @cleared="() => {selectedTagsBuffer = []; this.$emit('tagSelectionChange', []);}"
            />
            <Select2
                id="negative-tag-search"
                name="negative-tag-search"
                url="/tags"
                :additional_query_param="this.typeParameter()"
                model="negative-tags"
                :label="trans('global.tag.filter.negative_title')"
                :multiple="true"
                :selected="selectedNegativeTags"
                @selectedValue="(idArray) => {selectedNegativeTagsBuffer = idArray; this.$emit('negativTagSelectionChange', idArray);}"
                @cleared="() => {selectedNegativeTagsBuffer = []; this.$emit('negativTagSelectionChange', []);}"
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
            selectedNegativeTags: [],
            selectedTagsBuffer: [],
            selectedNegativeTagsBuffer: [],
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
            this.selectedNegativeTags = this.selectedNegativeTagsBuffer;
        },
    }
}
</script>