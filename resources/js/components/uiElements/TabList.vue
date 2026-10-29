<template>
    <div
        class="d-flex align-items-center"
        style="max-width: 100vw;"
    >
        <span v-if="showNavigators"
            class="pe-2"
        >
            <button
                class="btn btn-icon flex-shrink-0 text-secondary"
                role="button"
                tabindex="-1"
                @click="slide(false)"
            >
                <i class="fa fa-angle-left"></i>
            </button>
        </span>

        <div
            ref="tablist"
            class="position-relative d-flex flex-nowrap overflow-auto hide-scrollbars my-2"
            role="tablist"
            tabindex="-1"
            aria-label="Filter"
        >
            <div class="tablist-background position-absolute rounded-pill h-100"></div>
            <span
                :id="model + '-tablist'"
                class="d-flex z-1"
            >
                <span v-for="(tab, index) in tabs"
                    class="flex-shrink-0"
                >
                    <button v-if="tab.permission === undefined || checkPermission(tab.permission)"
                        :id="model + '-tab-' + index"
                        class="btn btn-tab bg-transparent border-transparent shadow-none"
                        :class="{ active: initialTab === index }"
                        type="button"
                        role="tab"
                        data-bs-toggle="pill"
                        :data-bs-target="model == 'subscribe' && ('#' + tab.filter + '_subscription')"
                        :tabindex="initialTab === index ? '0' : '-1'"
                        :aria-selected="initialTab === index"
                        @click="changeTab(index)"
                        @keydown.enter.space="changeTab(index)"
                    >
                        <i class="fa pe-2" :class="tab.icon"></i>
                        {{ getTranslatedText(tab.label) }}
                    </button>
                </span>
            </span>
        </div>

        <span v-if="showNavigators"
            class="ps-2"
        >
            <button
                class="btn btn-icon flex-shrink-0 text-secondary"
                role="button"
                tabindex="-1"
                @click="slide(true)"
            >
                <i class="fa fa-angle-right"></i>
            </button>
        </span>
    </div>
</template>
<script>
export default {
    name: "TabList",
    emits: ['change-tab'],
    props: {
        model: {
            type: String,
            required: true,
            description: "Singular name of the model to filter (e.g. 'kanban')",
        },
        tabs: {
            type: Array,
            required: true,
            description: "Array of Objects each containing 'icon', 'label' and optionally 'permission'",
        },
        initialTab: {
            type: Number,
            default: 0,
            description: "Set which tab should be initially active (index-based)",
        },
    },
    data() {
        return {
            showNavigators: false,
        }
    },
    mounted() {
        window.addEventListener('resize', this.checkTabListWidth);
        this.checkTabListWidth();
        this.changeTab(0, true); // initialize active-background position/width
    },
    unmounted() {
        window.removeEventListener('resize', this.checkTabListWidth);
    },
    methods: {
        changeTab(index, initial = false) {
            if (!initial) this.$emit('change-tab', this.tabs[index]);

            const tabRects = this.$el.querySelector('#' + this.model + '-tab-' + index).getBoundingClientRect();
            const leftOffset = this.$el.querySelector('#' + this.model + '-tablist').getBoundingClientRect().left;
            const backgroundElem = this.$el.querySelector('.tablist-background');

            backgroundElem.style.left = (tabRects.left - leftOffset) + 'px';
            let width = tabRects.width;
            // the modal-opening animation causes each element to be slightly bigger
            if (initial && document.getElementsByClassName('modal-mask')[0]?.contains(this.$el)) {
                width = width / 1.1;
            }
            backgroundElem.style.width = width + 'px';
        },
        slide(right) {
            const tabListLeftPos = this.$refs.tablist.getBoundingClientRect().left;
            const tabChildren = this.$el.querySelector('#' + this.model + '-tablist').children;
            const margin = 25; // min amount of pixels slider has to move
            let childLeftPos;

            if (right) {
                // check entries from left to right
                for (const child of tabChildren) {
                    childLeftPos = child.getBoundingClientRect().left;
                    if (childLeftPos > (tabListLeftPos + margin)) break;
                }
            } else {
                // check entries from right to left
                for (const child of [...tabChildren].reverse()) {
                    childLeftPos = child.getBoundingClientRect().left;
                    if (childLeftPos < (tabListLeftPos - margin)) break;
                }
            }

            const distance = childLeftPos - tabListLeftPos;
            this.$refs.tablist.scrollBy({ left: distance, behavior: 'smooth' });
        },
        checkTabListWidth() {
            // if the total width of the tabs exceeds the available width, show the navigator-arrows
            this.showNavigators = this.$refs.tablist.scrollWidth > this.$refs.tablist.offsetWidth;
        },
        getTranslatedText(label) {
            let trans = '';

            // label can be array
            if (typeof label !== 'string') {
                label.forEach(l => trans += this.trans(l) + ' ');
            } else {
                trans = this.trans(label);
            }

            return trans.trim();
        },
    },
}
</script>
<style>
.tablist-background {
    background-color: var(--bs-primary);
    transition: width 0.3s ease, left 0.3s ease;
}
</style>