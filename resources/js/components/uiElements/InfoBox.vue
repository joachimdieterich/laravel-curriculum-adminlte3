<template>
    <div class="col-6 mb-3">
        <div class="infobox">
            <div class="infobox-header">
                <button
                    type="button"
                    class="btn infobox-icon elevation-1"
                    :class="iconBackgroundClass"
                >
                    <i class="fa" :class="icon"></i>
                </button>
                <span class="flex-fill h3 mx-3 my-2">{{ text }}</span>
            </div>
            <div class="infobox-body">
                <div class="card-body p-1">
                    <ul>
                        <li>test</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'InfoBox',
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
            default: 'bg-info',
        },
    },
    data() {
        return {
            component_id: this.$.uid,
        }
    },
    mounted() {

    },
}
</script>
<style scoped>
.infobox {
    background-color: #fff;
    border-radius: 0.75rem;

    & > .infobox-header {
        display: flex;
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
        overflow-y: auto;
        max-height: 240px;
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