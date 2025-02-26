<template>
    <div
        id="searchbar"
        class="d-none input-group mx-3"
    >
        <input
            id="searchbar_input"
            class="form-control rounded-pill h-100 border-0"
            style="padding-right: 4rem;"
            type="search"
            :placeholder="trans('global.search')"
            :aria-label="trans('global.search')"
            v-model="filter"
            @keydown.enter="prepareEvent(true)"
        />
        <div class="position-relative">
            <button v-if="filter.length > 0"
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
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            filter: '',
            filtered: false,
            timer: null,
        }
    },
    methods: {
        prepareEvent(forced = false) {
            document.getElementById('searchbar_input').focus();
            clearTimeout(this.timer);

            if (forced) { // forced == Enter or button-click
                this.fireEvent();
                return;
            } else if (this.filter.length < 3) {
                if (this.filtered) this.removeFilter();
                return;
            }

            this.timer = setTimeout(() => {
                this.fireEvent();
            }, 250);
        },
        removeFilter() {
            this.filtered = false; // only throw this event once
            this.$eventHub.emit('filter', '');
        },
        fireEvent() {
            this.filtered = true;
            this.$eventHub.emit('filter', this.filter);
        },
        clearSearch() {
            this.filter = '';
            this.$el.getElementsByTagName('input')[0].focus();
        },
    },
    mounted() {
        this.$eventHub.on('showSearchbar', () => this.$el.classList.remove('d-none'));
    },
    watch: {
        filter() { this.prepareEvent(); }
    }
}
</script>
<style scoped>
#searchButton {
    background-color: #EAF099;
    z-index: 10;
    right: 0;
}
#searchButton::before {
    content: 'Suche';
    width: 0px;
    overflow: hidden;
    text-align: left;
    transition: width 0.2s ease-out;
}
#clearSearch {
    z-index: 20;
    right: 40px;
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
            padding: 0 96px 0 0 !important;

            & + div {
                > #clearSearch { display: none !important; }
                > #searchButton::before { width: 55px; }
            }
        }
    }
}
</style>