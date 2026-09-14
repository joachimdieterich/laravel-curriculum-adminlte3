<template>
    <span class="form-check form-switch">
        <input
            :id="id"
            class="form-check-input"
            type="checkbox"
            role="switch"
            :checked="checked"
            @change="$emit('update:checked', $event.target.checked)"
            switch
        >
        <label
            class="form-check-label"
            :for="id"
        >
            {{ trans(label) }}
        </label>
    </span>
</template>
<script>
export default {
    name: "Switch",
    emits: ['update:checked'],
    props: {
        id: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            required: true,
            description: 'translation-key to describe the switch'
        },
        checked: {
            type: Boolean,
            default: false,
        },
    },
}
</script>