<template>
    <div class="mb-3">
        <div class="infobox">
            <div class="infobox-header">
                <button
                    type="button"
                    class="btn infobox-icon elevation-1"
                    :class="iconBackgroundClass"
                >
                    <i class="fa" :class="icon"></i>
                </button>
                <span class="flex-fill h3 mx-3">{{ text }}</span>
            </div>
            <div class="infobox-body">
                <div v-for="entry in entries"
                    class="infobox-entry"
                >
                    <slot name="entry" :entry="entry">
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
    emits: ['error'],
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