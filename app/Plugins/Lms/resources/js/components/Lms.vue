<template>
    <div class="col-12 px-0"
         :id="'#lms_' + referenceable_id">
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
                    <span
                        v-if="isOwner(entry.owner_id)">
                        <button v-permission="'lms_delete'"
                                class="btn btn-flat py-0 px-2 pull-right"
                                @click.prevent="del(entry.id)">
                            <i class="fa fa-trash text-danger vuehover"></i>
                        </button>
                        <button v-permission="'lms_create'"
                                class="btn btn-flat py-0 px-2 pull-right"
                                @click.prevent="del(entry.id)">
                            <i class="fa fa-share-alt text-muted vuehover"></i>
                        </button>
                    </span>

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
            axios.delete('/lmsReferences/' + id)
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
        isOwner(id) {
            return (id == this.$userId) ? true : false;
        }

    },
    mounted() {

    },
    computed: {},

}
</script>
<style scoped>
.media_table,
.media_table tr:first-child,
.media_table tr:first-child td {
    border-top: 0px !important;
}
</style>
