<template>
        <div
            :id="id + '_form_group'"
            class="form-group c-select-form-group"
            :class="[(typeof css != 'undefined') ? css : '' ]"
        >
            <label v-if="showLabel"
                :for="id"
                :class="[classLeft]"
            >
                <span v-if="label != ''" :class="{'full-line': buttonNewLine}">{{ label }}</span>
                <span v-else>
                    {{ trans('global.' + model + '.title_singular') }}
                </span>
                <span v-if="multiple">
                    <slot name="buttons"></slot>
                </span>
            </label>

            <slot name="pre-dropdown"></slot>
            <v-select :ref="instance"
                      :options="groupedOptions? options: paginated"
                      :filterable="false"
                      :multiple="multiple"
                      :placeholder="placeholder"
                      label="label"
                      class="v-select-overflow"
                      @search="setFetchOptions"
                      @option:selecting="(selectedOption) => {return this.$emit('selectedValue', selectedOption);}"
            >
                <template v-slot:option="option">
                    <slot name="option" :option="option"></slot>
                </template>
                <template #list-footer>
                    <li v-show="hasNextPage" ref="load" class="loader">
                        {{ trans('global.loading') }}
                    </li>
                </template>
                <template #no-options="">
                    {{ trans('global.cselect.no_results') }}
                </template>
            </v-select>
        </div>
</template>

<script>
import {useToast} from "vue-toastification";
import Avatar from "../uiElements/Avatar.vue";

export default {
    name: "CSelect",
    components: {Avatar},
    emits: [
        'selectedValue'
    ],
    setup() {
        const toast = useToast();

        return {
            toast,
        }
    },
    props: {
        id: {
            type: String,
            default: 'c-select',
            required: true,
        },
        url: {
            type: String,
            default: '',
        },
        model: {
            type: String,
            required: false,
        },
        groupedOptions: {
            type: Boolean,
            required: false,
            default: false
        },
        css: {
            type: String,
            default: '',
        },
        showLabel: {
            type: Boolean,
            default: true,
        },
        classLeft: {
            type: String,
            default: 'p-0 col-sm-12',
        },
        label: {
            type: String,
            default: '',
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        placeholder: {
            type: String,
            default: window.trans.global.shareSearch,
        },
        buttonSizeClass: {
            type: String,
            default: 'btn-xs'
        },
        buttonNewLine: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            instance: null,
            limit: 25,
            searchLengthMinium: 3,
            search: '',
            page: 1,
            options: []
        }
    },
    computed: {
        paginated() {
            return this.options.slice(0, this.limit)
        },
        hasNextPage() {
            return this.paginated.length < this.options.length
        },
        fullUrl() {
            let fullUrl = this.url + '?search=' + this.search;
            if (!this.groupedOptions) {
                fullUrl += '&page=' + this.page;
            }

            return fullUrl;
        },
    },
    methods: {
        fetchOptions(loading) {
            loading(true);
            axios.get(this.fullUrl)
                .then((res) => {
                    loading(false);
                    this.options = res.data;
                })
                .catch(() => {
                    loading(false);
                    this.toast.error(this.trans('global.code_500'), {
                        timeout: 3000,
                        hideProgressBar: true,
                    });
                });
        },
        setFetchOptions (search, loading) {
            this.search = search;
            if (search.length >= this.searchLengthMinium) {
                this.fetchOptions(loading);
            }
        },
    },
}
</script>

<style>
.full-line {
    width: 100%;
    display: inline-block;
}
</style>
