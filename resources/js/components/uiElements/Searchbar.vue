<template>
    <div id="searchbar" class="d-none input-group">
        <input
            class="form-control border-0 rounded pr-5"
            type="search"
            :placeholder="trans('global.search')"
            :aria-label="trans('global.search')"
            v-model="filter"
            @keyup="checkFilter"/>
        <button
            class="btn h-100 position-absolute end-0"
            type="button"
            @click="checkFilter({key: 'Enter'})">
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
        checkFilter(e) {
            clearTimeout(this.timer);

            if (e.key == 'Enter') {
                this.throwEvent();
                return;
            }
            // else if (this.filter.length < 3 || this.filtered) {
            //     this.clearFilter();
            //     return;
            // }
            
            // this.timer = setTimeout(() => {
            //     this.throwEvent();
            // }, 750);
        },
        clearFilter() {
            this.filtered = false; // only throw this event once
            this.$eventHub.$emit('clearFilter');
        },
        throwEvent() {
            this.filtered = true;
            this.$eventHub.$emit('filter', this.filter);
        }
    },
    watch: {
        filter(newValue) {
            clearTimeout(this.timer);
            console.log(newValue, this.filtered);
            if (newValue.length < 3 || this.filtered) {
                this.clearFilter();
                return;
            }

            this.timer = setTimeout(() => {
                console.warn('New Event');
                this.throwEvent();
            }, 750);
        }
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