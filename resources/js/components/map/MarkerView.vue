<template>
    <div>
        <h1 class="sidebar-header  mb-3">
            {{ this.marker.title }}
            <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
        </h1>
        <h5 class="pt-2">{{ trans('global.author') }}</h5>
        <p>{{ this.marker.author }}</p>

        <h5 class="pt-2">{{ trans('global.description') }}</h5>
        <div class="pb-2"
             v-html="this.marker.description"></div>

        <h5 class="pt-2">{{ trans('global.marker.fields.link') }}</h5>
        <p>
            <a :href="this.marker.url">
                <span v-if="this.marker.url_title">
                    {{ this.marker.url_title }}
                </span>
                <span v-else>Link zum Projekt</span>
            </a>
        </p>

        <h5 class="pt-2">{{ trans('global.media.title') }}</h5>
        <p >-</p>
    </div>

</template>
<script>
export default {
  name: 'MarkerView',
    props: {
        marker: {
            default: null
        },
    },
    data() {
        return {
            component_id: this._uid,
        }
    },
    methods: {

    },
    mounted() {

    }

}
</script>
