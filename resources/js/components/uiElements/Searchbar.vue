<template>
    <div id="searchbar" class="d-none input-group">
        <input
            class="form-control border-0 rounded pr-5"
            type="search"
            :placeholder="trans('global.search')"
            :aria-label="trans('global.search')"
            v-model="filter"
            @keydown.enter="prepareEvent(true)"/>
        <button
            class="btn h-100 position-absolute end-0"
            type="button"
            @click="prepareEvent(true)">
            <span class="fa fa-search"></span>
        </button>
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
                this.throwEvent();
                return;
            } else if (this.filter.length < 3) {
                if (this.filtered) this.removeFilter();
                return;
            }
            
            this.timer = setTimeout(() => {
                this.throwEvent();
            }, 750);
        },
        removeFilter() {
            this.filtered = false; // only throw this event once
            this.$eventHub.$emit('removeFilter');
        },
        throwEvent() {
            this.filtered = true;
            this.$eventHub.$emit('filter', this.filter);
        }
    },
    watch: {
        filter() { this.prepareEvent(); }
    }
}
</script>
<style scoped>
button {
    background-color: #EAF099;
    z-index: 10;
    right: 0;
}
input[type="search"]::-webkit-search-cancel-button:hover { 
    cursor:pointer; 
}
</style>