<template>
        <slot name="pre-dropdown"></slot>
        <v-select ref="instance"
                  :options="options"
                  :filterable="false"
                  :multiple="multiple"
                  :placeholder="placeholder"
                  :label="label"
                  v-model="selectedOption"
                  class="v-select-overflow"
                  @search="setFetchOptions"
                  @open="onOpen"
                  @close="onClose"
                  @option:selected="(selectedOption) => {
                      this.selectedOption = selectedOption;
                      if (clearSearchOnSelect()) {
                          this.selectedOption = undefined
                      }

                      return this.$emit('selectedValue', selectedOption);
                  }"
                  :dropdown-should-open="(instance) => {
                      return this.searchLengthMinium === 0
                      ? instance.open && selectedOption !== null || (instance.open && !this.loading)
                      : search.length >= this.searchLengthMinium && !this.loading;
                  }"
                  :clear-search-on-blur="clearSearchOnSelect"
                  :loading="loading"
                  :searchable="searchable"
        >
            <template v-slot:option="option">
                <slot name="option" :option="option"></slot>
            </template>
            <template #list-footer>
                <li v-show="hasNextPage" ref="load" class="v-select-loader">
                    {{ trans('global.loading') }}
                </li>
            </template>
            <template #no-options="">
                <div v-show="!hasNextPage">
                    {{ trans('global.cselect.no_results') }}
                </div>
            </template>
        </v-select>
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

        // v-select controlling
        groupedOptions: {
            type: Boolean,
            required: false,
            default: false
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        searchable: {
            type: Boolean,
            default: true,
        },
        clearSearchOnSelect: {
            type: Function,
            default: function () {
                return false;
            }
        },

        // frontend
        label: {
            type: String,
            default: 'label',
        },
        placeholderKey: {
            type: String,
            default: 'pleaseSelect',
        },

        // search
        url: {
            type: String,
            default: '',
        },
        searchQueryParameter: {
            type: String,
            default: 'term'
        },
        handleFetchedData: {
            type: Function,
            default: function (getData) {
                return getData.results;
            }
        },
        handleFetchedSelectedFetchData: {
            type: Function,
            default: function (getData) {
                return getData[0];
            }
        },
        searchLengthMinium: {
            type: Number,
            default: 0
        },
        selected: {
            default: undefined
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            observer: null,
            limit: 25,
            search: '',
            page: 1,
            options: [],
            fetchTimer: null,
            loading: false,
            selectedOption: null,
            gotEmptyOrNotEnoughFetchResult: false,
            selectedLabel: '',
        }
    },
    mounted() {
        this.observer = new IntersectionObserver(this.infiniteScroll, {threshold: 0.8});

        if (this.selected != undefined) {
            this.fetchSelected();
        }
    },
    computed: {
        placeholder() {
            return window.trans.global[this.placeholderKey];
        },
        hasNextPage() {
            return this.options.length <= (this.page * this.limit) && !this.gotEmptyOrNotEnoughFetchResult
        },
        fullUrl() {
            let fullUrl = this.url + '?' + this.searchQueryParameter + '=' + this.search;
            if (!this.groupedOptions) {
                fullUrl += '&page=' + this.page;
            }

            return fullUrl;
        },
        fullSelectedUrl() {
            return this.url + '?selected=' + this.selected;
        }
    },
    methods: {
        handleFetchError(error) {
            this.loading = false;
            let message = this.trans('global.code_500');
            if (error?.response?.status === 400) {
                message = error.response.data;
            }

            this.toast.error(message, {
                timeout: 6000,
                hideProgressBar: true,
            });
        },
        async fetchOptions(addResult = false) {
            return axios.get(this.fullUrl)
                .then((res) => {
                    this.loading = false;
                    let data = this.handleFetchedData(res.data) ?? [];
                    this.gotEmptyOrNotEnoughFetchResult = data.length < this.limit;

                    this.page++;

                    if (addResult) {
                        this.options = this.options.concat(data);
                    } else {
                        this.options = data;
                    }

                    if (this.hasNextPage) {
                        this.observer.observe(this.$refs.load);
                    }
                })
                .catch(this.handleFetchError);
        },
        async fetchSelected() {
            return axios.get(this.fullSelectedUrl)
                .then((res) => {
                    this.selectedOption =  this.handleFetchedSelectedFetchData(res.data);
                })
                .catch(this.handleFetchError);
        },
        setFetchOptions (search) {
            this.search = search;

            // Only trigger GET-Request if search wasn't triggered again in the last 200ms
            if (search.length >= this.searchLengthMinium) {
                clearTimeout(this.fetchTimer);

                this.loading = true;
                this.fetchTimer = setTimeout(async () => {
                    await this.fetchOptions();
                }, 200);
            }
        },
        async onOpen() {
            // Simulate search on opening
            if (this.searchLengthMinium === 0) {
                this.page = 1;
                await this.fetchOptions();
            }
        },
        onClose() {
            this.observer.disconnect();
        },
        async infiniteScroll([{ isIntersecting, target }]) {
            if (isIntersecting) {
                const ul = target.offsetParent;
                const scrollTop = target.offsetParent.scrollTop;
                await this.fetchOptions( true);
                ul.scrollTop = scrollTop;
            }
        },
    },
}
</script>