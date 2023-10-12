<template>
    <div id="searchbar" class="d-none input-group mx-3">
        <input
            class="form-control border-0 rounded pr-5"
            type="search"
            :placeholder="trans('global.search')"
            :aria-label="trans('global.search')"
            v-model="filter"
            @keydown.enter="prepareEvent(true)"/>
        <div class="position-relative">
            <button
                class="btn h-100 position-absolute"
                type="button"
                @click="checkState()">
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
            this.$eventHub.$emit('filter', '');
        },
        fireEvent() {
            this.filtered = true;
            this.$eventHub.$emit('filter', this.filter);
        },
        checkState() {
            const notFocused = this.$el.firstChild.width == 0;

            if (window.innerWidth < 576 && notFocused) {
                this.$el.getElementsByTagName('input')[0].focus();
            } else {
                this.prepareEvent(true);
            }
        }
    },
    mounted() {
        this.$eventHub.$on('showSearchbar', () => this.$el.classList.remove('d-none'));
    },
    watch: {
        filter() { this.prepareEvent(); }
    }
}
</script>
<style scoped>
button {
    display: flex;
    align-items: center;
    background-color: #EAF099;
    z-index: 10;
    right: 0;
}
button::before {
    content: 'Suche';
    width: 0px;
    overflow: hidden;
    text-align: left;
    transition: .4s width;
}
input[type="search"]::-webkit-search-cancel-button:hover {
    cursor:pointer;
}
input[type="search"]:focus {
    width: 250px !important;
}
</style>
