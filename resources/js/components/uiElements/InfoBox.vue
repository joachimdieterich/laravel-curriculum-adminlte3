<template>
    <div class="mb-3">
        <div class="infobox">
            <div class="infobox-header">
                <button
                    type="button"
                    class="btn infobox-icon elevation-1"
                    :class="iconBackgroundClass"
                    @click="goToModel()"
                    @click.middle="goToModel(true)"
                >
                    <i class="fa" :class="icon"></i>
                </button>
                <span class="flex-fill h3 mx-3">{{ text }}</span>
                <button v-if="hasModal"
                    class="btn btn-icon btn-icon-big"
                    @click="openModal"
                >
                    <i class="fa fa-2x fa-plus"></i>
                </button>
            </div>
            <div class="infobox-body">
                <div v-for="entry in entries"
                    class="infobox-entry"
                >
                    <slot name="entry" :entry="entry">
                        <i v-if="!entry.grade"
                            class="fa text-secondary mr-2"
                            :class="entry.owner_id == $userId ? 'fa-user' : 'fa-share-alt'"
                        ></i>
                        <a :href="'/' + model + '/' + entry.id">
                            <span class="font-weight-bold">{{ entry.title }}</span>
                            <br/>
                            <span v-if="entry.grade"
                                class="text-muted"
                            >
                                {{ entry.grade.title }}
                            </span>
                        </a>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'InfoBox',
    emits: ['open-modal', 'error'],
    props: {
        model: {
            type: String,
            required: true,
        },
        text: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            required: true,
        },
        iconBackgroundClass: {
            type: String,
            default: 'bg-blue',
        },
        href: {
            type: String,
            default: null, // defaults to this.model
        },
        hasModal: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            entries: [],
        }
    },
    mounted() {
        this.getEntries();
    },
    methods: {
        getEntries() {
            axios.get('/home/' + this.model)
                .then(response => {
                    this.entries = response.data;
                })
                .catch(error => {
                    this.$emit('error', error);
                });
        },
        goToModel(newTab = false) {
            if (newTab) window.open(this.href || '/' + this.model, '_blank');
            else window.location.href = this.href || '/' + this.model;
        },
        openModal() {
            this.$emit('open-modal', this.model);
        },
    },
}
</script>
<style scoped>
.infobox {
    background-color: #fff;
    border-radius: 0.75rem;

    & > .infobox-header {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        
        & > .infobox-icon {
            color: #fff;
            height: 70px;
            width: 70px;
            font-size: 2rem;
            text-align: center;
            border-radius: 0.75rem;
        }
    }
    & > .infobox-body {
        padding: 0.75rem;
        overflow-y: auto;
        max-height: 240px;

        & > .infobox-entry:not(:last-child) {
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 0.5rem;
        }
    }
}

@supports(corner-shape: squircle) {
    .infobox {
        border-radius: 1.5rem;
        corner-shape: squircle;

        & > .infobox-header > .infobox-icon {
            border-radius: 1.5rem;
            corner-shape: squircle;
        }
    }
}
</style>