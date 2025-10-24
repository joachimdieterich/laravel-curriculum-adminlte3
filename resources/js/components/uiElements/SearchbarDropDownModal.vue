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
                @selectedValue="(idArray) => {this.$emit('tagSelectionChange', idArray); this.selectedTags = idArray;}"
                @cleared="() => {this.$emit('tagSelectionChange', []);}"
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
    mounted() {
        this.globalStore = useGlobalStore();
    },
    data() {
        return {
            searchTagModelContext: null,
            selectedTags: [],
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
            let searchTagModelContext = this.globalStore.getItem('searchTagModelContext');
            if (searchTagModelContext === null) {
                console.log(2);
                console.error('No searchTagModelContext is defined in the global store.')
            }

            return {
                'type': searchTagModelContext,
            };
        },
    },
    computed: {
        close: function() {
            return this.id + '-close';
        },
    }
}
</script>