<template>
    <div>
        <div v-if="entries.length > 0"
            class="p-3 border-bottom"    
        >
            {{ trans('global.lms.title_singular') }}
        </div>
        <div v-for="entry in entries"
            class="d-flex justify-content-between px-3 py-2 border-top bg-gray-light"
        >
            <a
                :href="entry.value.course_item.url"
                target="_blank"
                class="d-flex align-items-center gap-1 link-underline-hover"
            >
                <img v-if="entry.value.course_item.modicon"
                    :src="entry.value.course_item.modicon"
                    height="20px"
                />
                <i v-else class="fa fa-graduation-cap link-muted"></i>
                {{ entry.value.course_item?.name }}
            </a>
            <button v-if="editable"
                v-permission="'lms_delete'"
                class="btn btn-icon text-danger"
                @click="destroy(entry.id)"
            >
                <i class="fa fa-trash"></i>
            </button>
        </div>

        <div v-if="editable"
            v-permission="'lms_create'"
        >
            <button
                type="button"
                class="btn btn-default border-0 mt-2 rounded-pill"
                style="padding: 0.75rem 1.25rem;"
                @click="openModal()"
            >
                <i class="fa fa-plus pe-1"></i>
                {{ trans('global.lms.add') }}
            </button>
        </div>
    </div>
</template>
<script>
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
        editable: {
            type: Boolean,
            default: false,
        },
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
                .catch(e => {
                    console.log(e);
                });
        },
        openModal() {
            this.globalStore.showModal('lms-modal',{
                referenceable_type: this.referenceable_type,
                referenceable_id: this.referenceable_id,
            });
        },
        destroy(id) {
            axios.delete('/lmsReferences/' + id)
                .then(res => {
                    const index = this.entries.findIndex(item => item.id === id);
                    this.entries.splice(index, 1);
                })
                .catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
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