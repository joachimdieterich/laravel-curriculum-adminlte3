<template>
    <slot name="label"></slot>
    <v-select
        ref="instance"
        :input-id="inputId"
        class="v-select-overflow"
        :options="options"
        :label="label"
        :loading="loading"
        :filterable="false"
        :multiple="multiple"
        :searchable="searchable"
        :placeholder="trans(placeholder)"
        v-model="selectedOption"
        :clear-search-on-blur="clearSearchOnSelect"
        :dropdown-should-open="instance => {
            return searchLengthMinium === 0
                ? instance.open && selectedOption !== null || (instance.open && !loading)
                : search.length >= searchLengthMinium && !loading;
        }"
        @open="onOpen"
        @close="onClose"
        @search="setFetchOptions"
        @update:model-value="newOption => {
            selectedOption = clearSearchOnSelect() ? undefined : newOption;

            $emit('selectedValue', newOption);
        }"
    >
        <!-- pass the slot to the parent -->
        <template #option="option">
            <slot name="option" :option="option"></slot>
        </template>
        <slot name="list-footer">
            <li v-show="hasNextPage" ref="load" class="v-select-loader">
                {{ trans('global.loading') }}
            </li>
        </slot>
        <slot name="no-options">
            <div v-show="!hasNextPage">
                {{ trans('global.cselect.no_results') }}
            </div>
        </slot>
    </v-select>
</template>
<script>
export default {
    name: "CSelect",
    emits: ['selectedValue'],
    props: {
        // v-select controlling
        groupedOptions: {
            type: Boolean,
            default: false,
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
            },
        },

        // frontend
        inputId: { type: String },
        label: {
            type: String,
            default: 'label',
        },
        placeholder: {
            type: String,
            default: 'global.pleaseSelect',
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
            },
        },
        handleFetchedSelectedFetchData: {
            type: Function,
            default: function (getData) {
                return getData[0];
            },
        },
        searchLengthMinium: {
            type: Number,
            default: 0,
        },
        selected: {
            default: undefined,
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
        }
    },
    mounted() {
        this.observer = new IntersectionObserver(this.infiniteScroll, {threshold: 0.8});

        if (this.selected != undefined) {
            this.fetchSelected();
        }
    },
    computed: {
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
            return axios.get(this.url + '?selected=' + this.selected)
                .then((res) => {
                    this.selectedOption = this.handleFetchedSelectedFetchData(res.data);
                })
                .catch(this.handleFetchError);
        },
        setFetchOptions (search) {
            this.search = search;

            // Only trigger GET-Request if search wasn't triggered again in the last 300ms
            if (search.length >= this.searchLengthMinium) {
                clearTimeout(this.fetchTimer);

                this.loading = true;
                this.fetchTimer = setTimeout(async () => {
                    await this.fetchOptions();
                }, 300);
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
                await this.fetchOptions(true);
                ul.scrollTop = scrollTop;
            }
        },
    },
}
</script>