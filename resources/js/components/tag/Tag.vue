<template>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="m-0">
                            <i class="fas fa-user-tag mr-1"></i>
                            {{ this.tag.translation }}
                        </h5>
                    </div>
                </div>

                <div class="card-body">
                    <p v-if="this.tag.type === null">Typ: Global</p>
                    <p v-else>Typ: {{ trans('global.tag.types.' + this.tag.type)}}</p>
                </div>

                <div class="card-footer">
                    <small class="float-right">
                        {{ this.formatDate(this.tag.updated_at) }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    name: "tag",
    props: {
        tag: {
            default: null,
        },
    },
    data() {
        return {
            componentId: this.$.uid,
        }
    },
}
</script>