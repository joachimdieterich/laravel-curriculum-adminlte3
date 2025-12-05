<template>
    <div class="d-flex align-items-center">
        <span>{{ message }}</span>
        <button
            type="button"
            class="btn btn-default rounded-pill ml-3"
            @click="$emit('button-clicked'); $emit('close-toast')"
        >
            {{ buttonText }}
        </button>
    </div>
</template>
<script>
export default {
    props: {
        message: {
            type: String,
            required: true,
        },
        buttonText: {
            type: String,
            default: 'OK',
        },
    },
}
</script>