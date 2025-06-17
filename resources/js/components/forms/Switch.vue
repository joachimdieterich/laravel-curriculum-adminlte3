<template>
    <div :id="id + '_form_group'">
        <span class="custom-control custom-switch custom-switch-on-green">
            <input
                :id="id"
                :name="id"
                :checked="checked"
                @change="$emit('update:checked', !checked)"
                type="checkbox"
                class="custom-control-input pt-1"
            />
            <label class="custom-control-label" :for="id">{{ label }}</label>
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
            required: true,
        },
        label: {
            type: String,
            default: '',
        },
        checked: {
            type: Boolean,
            default: false,
        },
    },
}
</script>