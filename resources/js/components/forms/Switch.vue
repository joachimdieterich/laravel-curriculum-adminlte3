<template>
    <span class="form-check form-switch">
        <input
            :id="id"
            class="form-check-input"
            type="checkbox"
            role="switch"
            :checked="modelValue"
            @change="$emit('update:modelValue', $event.target.checked)"
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
    emits: ['update:modelValue'],
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
        modelValue: {
            type: Boolean,
            default: false,
        },
    },
}
</script>