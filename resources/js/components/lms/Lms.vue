<template>
    <div class="col-12 px-0"
         :id="'#lms_' + referenceable_id">
        <table v-if="this.entries.length"
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
                                class="btn btn-flat py-0 px-3 pull-right"
                                @click.prevent="share(entry.id)">
                            <i class="fa fa-share-alt text-muted vuehover"></i>
                        </button>
                    </span>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td
                    class="py-2 link-muted text-sm pointer"
                    v-permission="'lms_create'"
                    @click.prevent="this.globalStore?.showModal('lms-modal');">
                    <i class="fa fa-plus px-2 "></i> {{ trans('global.lms.add') }}
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        'referenceable_type': String,
        'referenceable_id': Number,
    },
    setup () { //use database store
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            entries: [],
            errors: {}
        }
    },
    methods: {
         loaderEvent() {
             axios.post('/lmsReferences/get', {
                 plugin: 'moodle',
                 ws_function: 'show',
                 referenceable_type: this.referenceable_type,
                 referenceable_id: this.referenceable_id,
             })
             .then(r => {
                 this.entries = r.data
             })
             .catch(err => {
                 console.log(err.response);
             });

        },
        del(id) {
            axios.delete('/lmsReferences/' + id)
                .then(res => {
                    const index = this.entries.findIndex(            // Find the index of the status where we should replace the item
                        item => item.id === id
                    );
                    this.entries.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        share(id) {
            this.$eventHub.emit('shareLms', id);
        },
        isOwner(id) {
            return (id == this.$userId) ? true : false;
        }
    },
    mounted() {},
    computed: {},
}
</script>
