<template>
    <div
        :id="'#lms_' + referenceable_id"
        class="col-12 px-0"
    >
        <table v-if="entries.length"
            id="sidebar_media_datatable"
            class="table table-hover datatable media_table"
        >
            <tr v-for="entry in entries">
                <td>
                    <a
                        :href="entry.value.course_item.url"
                        target="_blank"
                    >
                        <img v-if="entry.value.course_item.modicon" :src="entry.value.course_item.modicon" height="16px"/>
                        <i v-else class="fa fa-graduation-cap link-muted"></i>
                        {{ entry.value.course_item?.name }}
                    </a>
                    <span v-if="entry.owner_id == $userId">
                        <button
                            v-permission="'lms_delete'"
                            class="btn btn-flat py-0 px-2 pull-right"
                            @click.prevent="del(entry.id)"
                        >
                            <i class="fa fa-trash text-danger vuehover"></i>
                        </button>
                        <button
                            v-permission="'lms_create'"
                            class="btn btn-flat py-0 px-3 pull-right"
                            @click.prevent="share(entry.id)"
                        >
                            <i class="fa fa-share-alt text-muted vuehover"></i>
                        </button>
                    </span>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td
                    v-permission="'lms_create'"
                    class="py-2 link-muted text-sm pointer"
                    @click.prevent="create()"
                >
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
        referenceable_type: {
            type: String,
            default: null,
        },
        referenceable_id: {
            type: Number,
            default: null,
        },
    },
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            entries: [],
            lms_url: null,
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
                this.lms_url = r.data.lms_url;
                this.entries = r.data.entries;
            })
            .catch(err => {
                console.log(err.response);
            });
        },
        create() {
            this.globalStore?.showModal('lms-modal',{
                referenceable_type: this.referenceable_type,
                referenceable_id: this.referenceable_id,
            });
        },
        del(id) {
            axios.delete('/lmsReferences/' + id)
                .then(res => {
                    const index = this.entries.findIndex(item => item.id === id);
                    this.entries.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        share(id) {
            this.$eventHub.emit('shareLms', id);
        },
    },
    mounted() {
        this.$eventHub.on('lms-added', newContent => {
            this.loaderEvent();
        });

        this.$eventHub.on('lms-updated', newContent => {
            this.loaderEvent();
        });
    },
}
</script>