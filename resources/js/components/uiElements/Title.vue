<template>
    <div :id="component_id">
        <slot name="title"></slot>
        <slot id="titleToolbar"
              name="toolbar"></slot>
        <div id="customTitle"></div>
    </div>
</template>

<script>
import { storeToRefs } from 'pinia';
import { useDatatableStore } from "../../store/datatables";
export default {
    props: {

    },
    setup () { //use database store
        const store = useDatatableStore();
        const { getDatatable } = storeToRefs(store);
        const { isSelected } = storeToRefs(store);
        return {
            store
        }
    },
    data() {
        return {
            component_id: this._uid,
        }
    },

    mounted() {

    },

    methods: {

    }
}
</script>
