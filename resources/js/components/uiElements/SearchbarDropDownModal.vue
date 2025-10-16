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
                :additional_query_param="this.typeParameter"
                model="tags"
                :label="trans('global.tag.title')"
                :multiple="true"
                @selectedValue="(idArray) => {this.$emit('tagSelectionChange', idArray);}"
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
        this.searchTagModelContext = this.globalStore.getItem('searchTagModelContext');
    },
    data() {
        return {
            searchTagModelContext: null
        }
    },
    props: {
        show: {
            type: Boolean,
            required: true
        },
    },
    computed: {
        close: function() {
            return this.id + '-close';
        },
        typeParameter: function () {
            return {
                'type': this.searchTagModelContext,
            };
        },
    }
}
</script>

<style scoped>
</style>