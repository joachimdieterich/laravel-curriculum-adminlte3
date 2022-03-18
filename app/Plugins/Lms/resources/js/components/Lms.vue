<template>
    <div class="col-12 px-0">

        <table
            id="sidebar_media_datatable"
            class="table table-hover datatable media_table">
            <tr v-for="entry in entries">
                <td v-if="typeof entry.value.course_item.name != 'undefined'">
                    <a :href="entry.value.course_item.url"
                       target="_blank">
                        <img :src="entry.value.course_item.modicon" height="16px">
                        {{ entry.value.course_item.name }}
                    </a>


                    <i v-permission="'lms_delete'"
                       @click.prevent="del(entry.id)"
                       class="fa fa-trash px-2 text-danger pull-right pointer"></i>
                    <i v-permission="'lms_create'"
                       @click.prevent="share('subscribe-modal', entry.id)"
                       class="fa fa-share-alt px-2 pull-right pointer"></i>
                </td>
            </tr>
            <tr>
                <td
                    class="py-2 link-muted text-sm pointer"
                    v-permission="'lms_create'"
                    @click.prevent="open('lms-modal')">
                    <i class="fa fa-plus px-2 "></i> {{ trans('global.lms.add') }}
                </td>
            </tr>
        </table>
    </div>
</template>


<script>

export default {
    props: {
        'referenceable_type': String,
        'referenceable_id': Number,
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
                this.entries = (await axios.post('/lmsReferences/get', {
                    plugin: 'moodle',
                    ws_function: 'show',
                    referenceable_type: this.referenceable_type,
                    referenceable_id: this.referenceable_id,
                })).data.message;
            } catch (error) {
                console.log(error.response);
            }
        },
        del(id) {
            axios.delete('/lmsReferenceSubscriptions/' + id)
                .then(res => { // Tell the parent component we've added a new task and include it
                    const index = this.entries.findIndex(            // Find the index of the status where we should replace the item
                        item => item.id === id
                    );
                    this.entries.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },

        open(modal) {
            this.$modal.show(modal, {
                'referenceable_type': this.referenceable_type,
                'referenceable_id': this.referenceable_id,
            });
        },
        share(modal, id) {
            this.$modal.show(modal, {
                'modelId': id,
                'modelUrl': 'lmsReference'
            });
        },

    },
    mounted() {

    },
    computed: {},

}
</script>
