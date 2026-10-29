<template>
    <div
        class="d-flex"
        style="max-width: 100vw;"
    >
        <span v-if="showNavigators"
            class="d-flex align-items-center pe-2 border-bottom"
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
            ref="navlist"
            class="flex-fill overflow-x-auto hide-scrollbars"
        >
            <ul
                class="nav nav-tabs align-items-center flex-nowrap"
                role="tablist"
            >
                <li v-for="(nav, index) in navs"
                    class="nav-item"
                    role="presentation"
                >
                    <button v-if="nav.permission === undefined || checkPermission(nav.permission)"
                        :id="nav.target + '-tab'"
                        class="d-flex align-items-center nav-link link-muted text-nowrap"
                        :class="initialTab === index && 'active'"
                        data-bs-toggle="tab"
                        :data-bs-target="'#' + nav.target"
                        type="button"
                        role="tab"
                        :aria-controls="nav.target"
                        :aria-selected="initialTab === index"
                    >
                        <i class="fa" :class="nav.icon"></i>
                        <div
                            class="navlist-label-wrapper"
                            :class="!help && 'hide-label'"
                        >
                            <span class="ps-2">{{ trans(nav.label) }}</span>
                        </div>
                    </button>
                </li>
            </ul>
        </div>

        <span v-if="showNavigators"
            class="d-flex align-items-center ps-2 border-bottom"
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

        <span class="d-print-none d-flex align-items-center border-bottom">
            <button
                type="button"
                class="btn btn-icon text-secondary"
                data-bs-toggle="tooltip"
                :data-bs-title="trans('global.toggle_navigation')"
                @click="toggleHelp()"
            >
                <i class="fa fa-question"></i>
            </button>
        </span>
    </div>
</template>
<script>
export default {
    name: 'NavList',
    props: {
        navs: {
            type: Array,
            required: true,
            description: "Array of Objects each containing 'icon', 'target', 'label' and optionally 'permission'",
        },
        initialTab: {
            type: Number,
            default: 0,
            description: "Set which tab should be initially shown (index-based)",
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            showNavigators: false,
            help: true,
        }
    },
    mounted() {
        window.addEventListener('resize', this.checkNavListWidth);
        this.checkNavListWidth();

        // set each label's actual width as css-width to allow width-transitions
        this.$nextTick(() => {
            this.$el.querySelectorAll('.navlist-label-wrapper').forEach(wrapper => {
                wrapper.style.width = wrapper.getBoundingClientRect().width + 'px';
            });
        });
    },
    unmounted() {
        window.removeEventListener('resize', this.checkNavListWidth);
    },
    methods: {
        slide(right) {
            const navListLeftPos = this.$refs.navlist.getBoundingClientRect().left;
            const navChildren = this.$refs.navlist.firstChild.children;
            const margin = 25; // min amount of pixels slider has to move
            let childLeftPos;

            if (right) {
                // check entries from left to right
                for (const child of navChildren) {
                    childLeftPos = child.getBoundingClientRect().left;
                    if (childLeftPos > (navListLeftPos + margin)) break;
                }
            } else {
                // check entries from right to left
                for (const child of [...navChildren].reverse()) {
                    childLeftPos = child.getBoundingClientRect().left;
                    if (childLeftPos < (navListLeftPos - margin)) break;
                }
            }

            const distance = childLeftPos - navListLeftPos;
            this.$refs.navlist.scrollBy({ left: distance, behavior: 'smooth' });
        },
        toggleHelp() {
            this.help = !this.help;

            let remainingAnimationTime = 300;
            const checking = setInterval(() => {
                this.checkNavListWidth();
                remainingAnimationTime -= 50;

                if (remainingAnimationTime <= 0) clearInterval(checking);
            }, 50);
        },
        checkNavListWidth() {
            // if the total width of the navs exceeds the available width, show the navigator-arrows
            this.showNavigators = this.$refs.navlist.scrollWidth > this.$refs.navlist.offsetWidth;
        },
    },
}
</script>
<style>
button.nav-link:not(.active) { border-bottom: 1px solid var(--bs-border-color); }
.navlist-label-wrapper {
    overflow: hidden;
    transition: width 0.3s ease;

    &.hide-label { width: 0px !important; }
}
</style>