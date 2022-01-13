<template>
    <div class="col-12 px-0">
        <div class="card-tools"
             v-can="'lms_create'"
        >
            <button
                class="dropdown-item px-3"
                @click.prevent="open('lms-modal')">
                <i class="fa fa-plus pull-right link-muted""></i>
            </button>
        </div>

        <div class="todo-list"
             v-for="entry in entries">
            <div class="p-2"
                 v-if="typeof entry.value.course_item.name != 'undefined'">
                <a :href="entry.value.course_item.url"
                   target="_blank">
                    <img :src="entry.value.course_item.modicon" height="16px">
                    {{ entry.value.course_item.name }}
                </a>
            </div>

        </div>

    </div>
</template>


<script>

export default {
    props: {
        'subscribable_type': String,
        'subscribable_id': Number,
    },
    data() {
        return {
            entries: [],

            errors: {}
        }
    },
    methods: {
        async loaderEvent() {
            try {
                this.entries = (await axios.post('/lmsSubscriptions/get', {
                    plugin: 'Moodle',
                    ws_function: 'show',
                    subscribable_type: this.subscribable_type,
                    subscribable_id: this.subscribable_id,
                })).data.message;
            } catch (error) {
                //this.errors = error.response.data.errors;
            }
        },

        open(modal) {
            this.$modal.show(modal, {
                'subscribable_type': this.subscribable_type,
                'subscribable_id': this.subscribable_id
            });
        },

    },
    mounted() {

    },
    computed: {},

}
</script>
