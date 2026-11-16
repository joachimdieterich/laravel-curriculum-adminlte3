<template>
    <div class="px-3">
        <div class="col-lg-4 col-12 mb-3 rounded-3 shadow-layout">
            <div class="d-flex align-items-center justify-content-between p-2 text-bg-primary rounded-top-3">
                <h5 class="m-0">
                    <i class="fa fa-user-tag mx-1"></i>{{ tag.translation }}
                </h5>
            </div>

            <div class="px-3 py-2 bg-white rounded-bottom-3">
                <small class="text-muted">{{ tag.updated_at }}</small>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "Tag",
    props: {
        tag: {
            type: Object,
            default: null,
        },
    },
}
</script>