<template>
    <div
        id="searchbar"
        class="d-none input-group mx-3"
    >
        <input
            id="searchbar_input"
            class="form-control rounded-pill h-100 border-0 search-field"
            type="search"
            :placeholder="trans('global.search')"
            :aria-label="trans('global.search')"
            v-model="filter.searchString"
            @keydown.enter="prepareEvent(true)"
        />
        <div class="position-relative">
            <button v-if="filter.searchString.length > 0"
                id="clearSearch"
                class="btn position-absolute d-flex align-items-center h-100"
                type="button"
                @click="clearSearch()"
            >
                <span class="fa fa-xmark"></span>
            </button>
            <button
                id="searchButton"
                class="btn position-absolute d-flex align-items-center rounded-pill h-100 border-0"
                type="button"
                @click="prepareEvent()"
            >
                <span class="fa fa-search"></span>
            </button>
            <button
                id="extended-search-button"
                :class="extendedSearchButtonClasses"
                type="button"
                @click="toggleModal"
            >
                <span class="fa fa-filter"></span>
            </button>
        </div>
        <SearchbarDropDownModal
            :show="showTagModal"
            @modal-close="toggleModal"
            @tagSelectionChange="(idArray) => {this.filter.tags = idArray;}"
        >

        </SearchbarDropDownModal>
    </div>
</template>
<script>
import SearchbarDropDownModal from "./SearchbarDropDownModal.vue";

export default {
    components: {SearchbarDropDownModal},
    data() {
        return {
            filter: {
                searchString: '',
                tags: []
            },
            filtered: false,
            timer: null,
            showTagModal: false,
        }
    },
    methods: {
        prepareEvent(forced = false) {
            document.getElementById('searchbar_input').focus();
            clearTimeout(this.timer);

            if (forced) { // forced == Enter or button-click
                this.fireEvent();
                return;
            } else if (this.filter.searchString.length < 3 && this.filter.tags.length == 0) {
                if (this.filtered) this.removeFilter();
                return;
            }

            this.timer = setTimeout(() => {
                this.fireEvent();
            }, 250);
        },
        removeFilter() {
            this.filtered = false; // only throw this event once
            this.$eventHub.emit('filter', this.filter);
        },
        fireEvent() {
            this.filtered = true;
            this.$eventHub.emit('filter', this.filter);
        },
        clearSearch() {
            this.filter.searchString = '';
            this.filter.tags = [];
            this.$el.getElementsByTagName('input')[0].focus();
        },
        toggleModal() {
            this.showTagModal = !this.showTagModal;
        }
    },
    computed: {
        extendedSearchButtonClasses() {
            let color = ' non-active-extended-search-button';
            if (this.showTagModal) {
                color = ' active-extended-search-button';
            }

            return 'btn position-absolute d-flex align-items-center rounded-pill h-100 border-0' + color;
        }
    },
    mounted() {
        this.$eventHub.on('showSearchbar', () => this.$el.classList.remove('d-none'));
    },
    watch: {
        'filter.searchString': function() { this.prepareEvent(); },
        'filter.tags': function() { this.prepareEvent(); }
    }
}
</script>
<style scoped>
#searchButton {
    background-color: #EAF099;
    z-index: 10;
    right: 36px;
}
#searchButton::before {
    content: 'Suche';
    width: 0;
    overflow: hidden;
    text-align: left;
    transition: width 0.2s ease-out;
}
.non-active-extended-search-button {
    background-color: #EAF099;
}
.active-extended-search-button {
    background-color: #007bff;
}
#extended-search-button {
    z-index: 9;
    right: 0;
    padding-left: 48px;
}
.search-field {
    padding-right: 9rem;
}
#clearSearch {
    z-index: 20;
    right: 80px;
}
#clearSearch:focus {
    box-shadow: none;
}
input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-cancel-button,
input[type="search"]::-webkit-search-results-button,
input[type="search"]::-webkit-search-results-decoration {
  -webkit-appearance: none;
}
@media only screen and (max-width: 400px) {
    /* more specific selector to overwrite 576px rules */
    div#searchbar > input {
        transition: width 0.5s ease-out, padding 0.5s ease !important;

        &:not(:focus-within) {
            padding: 0 40px 0 0 !important;

            & + div > #searchButton::before { content: none; }
        }
    }
}
@media only screen and (max-width: 576px) {
    #searchbar {
        > input {
            width: calc(100vw - 183px);
            max-width: 248px;
            transition: width 0.5s ease-out, padding 0.4s ease;
        }
        &:not(:focus-within) > input {
            width: 0;
            padding: 0 95px 0 0 !important;

            & + div {
                > #clearSearch { display: none !important; }
                > #searchButton::before { width: 55px; }
            }
        }
    }
}
</style>