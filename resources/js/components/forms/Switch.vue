<template>
    <div :id="this.id + '_form_group'"
         class="form-group pt-3">
        <span class="custom-control custom-switch custom-switch-on-green">
            <input
                :id="this.id"
                :name="this.id"
                :checked="checked"
                @click="this.$emit('update:checked', !checked)"
                type="checkbox"
                class="custom-control-input pt-1 "/>
            <label class="custom-control-label " for="global" > {{ this.label}}</label>
        </span>
    </div>
</template>

<script>
export default {
    name: "Switch",
    props: {
        id: {
            type: String,
            default: 'switch',
            required: true
        },
        label: {
            type: String,
            default: ''
        },
        checked: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {}
    },
    mounted() {

    },
    methods: {

    },
    components: {

    }
}
</script>

<style scoped>

</style>
