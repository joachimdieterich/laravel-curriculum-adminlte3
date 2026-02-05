<template>
    <div class="d-flex align-items-center px-3">
        <span v-if="showNavigators"
            class="pr-2"
        >
            <button
                class="btn btn-icon flex-shrink-0"
                role="button"
                tabindex="-1"
                @click="slide(false)"
            >
                <i class="fas fa-angle-left"></i>
            </button>
        </span>

        <div
            :id="model + '-filter'"
            class="d-flex flex-nowrap overflow-auto hide-scrollbars py-2"
            role="tablist"
            tabindex="-1"
            aria-label="Filter"
        >
            <span v-for="(tab, index) in tabs"
                :key="tab"
                class="flex-shrink-0"
            >
                <button v-if="(tab !== 'owner' && tab !== 'shared-by-me') || checkPermission(model + '_create')"
                    :id="model + '-filter-' + tab"
                    class="btn btn-tab"
                    :class="{ active: activeTab === tab }"
                    role="tab"
                    :tabindex="index === 0 ? '0' : '-1'"
                    :aria-selected="activeTab === tab"
                    @click="$emit('change-tab', tab)"
                    @keydown.enter.space="$emit('change-tab', tab)"
                    @keydown.left.right.prevent="moveFocus($event)"
                >
                    <i class="fas pr-2" :class="tabIcon(tab)"></i>
                    {{ tabText(tab) }}
                </button>
            </span>
        </div>

        <span v-if="showNavigators"
            class="pl-2"
        >
            <button
                class="btn btn-icon flex-shrink-0"
                role="button"
                tabindex="-1"
                @click="slide(true)"
            >
                <i class="fas fa-angle-right"></i>
            </button>
        </span>
    </div>
</template>
<script>
import { active } from 'd3';

export default {
    name: "TabList",
    emits: ['change-tab'],
    props: {
        model: {
            type: String,
            required: true,
            title: "Singular name of the model to filter (e.g. 'kanban')",
        },
        modelIcon: {
            type: String,
            required: true,
            title: "FontAwesome icon class used to identify the model",
        },
        tabs: {
            type: Array,
            default: ['all', 'owned', 'shared_with_me', 'shared_by_me'],
            title: "Possible String values: 'favourite', 'hidden', 'all', 'owned', 'shared_with_me', 'shared_by_me'",
        },
        activeTab: {
            type: String,
            required: true,
            title: "The active tab needs to be changed by the parent component",
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
    },
    unmounted() {
        window.removeEventListener('resize', this.checkTabListWidth);
    },
    methods: {
        tabIcon(tab) {
            switch (tab) {
                case 'favourite':
                    return 'fa-heart';
                case 'hidden':
                    return 'fa-eye-slash';
                case 'all':
                    return this.modelIcon;
                case 'owner':
                    return 'fa-user';
                case 'shared_with_me':
                    return 'fa-paper-plane';
                case 'shared_by_me':
                    return 'fa-share-nodes';
                default:
                    return '';
            }
        },
        tabText(tab) {
            switch (tab) {
                case 'favourite':
                    return this.trans('global.tag.favourite.plural');
                case 'hidden':
                    return this.trans('global.tag.hidden.plural');
                case 'all':
                    return this.trans('global.all') + ' ' + this.trans('global.kanban.title');
                case 'owner':
                    return this.trans('global.my') + ' ' + this.trans('global.kanban.title');
                case 'shared_with_me':
                    return this.trans('global.shared_with_me');
                case 'shared_by_me':
                    return this.trans('global.shared_by_me');
                default:
                    return tab;
            }
        },
        slide(right) {
            const tabListElement = document.getElementById(this.model + '-filter');
            const tabListLeftPos = tabListElement.getClientRects()[0].left;
            const tabChildren = tabListElement.children;
            let childLeftPos;

            if (right) {
                // check entries from left to right
                for (let i = 0; i < tabChildren.length; i++) {
                    childLeftPos = tabChildren[i].getClientRects()[0].left;
                    if (Math.round(childLeftPos) > tabListLeftPos) break;
                }
            } else {
                // check entries from right to left
                for (let i = tabChildren.length - 1; i >= 0; i--) {
                    childLeftPos = tabChildren[i].getClientRects()[0].left;
                    if (Math.round(childLeftPos) < tabListLeftPos) break;
                }
            }

            const distance = childLeftPos - tabListLeftPos;
            tabListElement.scrollBy({ left: distance, behavior: 'smooth' });
        },
        checkTabListWidth() {
            const tabListElement = document.getElementById(this.model + '-filter');
            // if the total width of the tabs exceeds the available width, show the navigator-arrows
            this.showNavigators = tabListElement.scrollWidth > tabListElement.offsetWidth;
        },
        moveFocus(e) {
            const currentTab = e.target;

            if (e.key === 'ArrowRight') {
                const nextTab = currentTab.parentElement.nextElementSibling ?? currentTab.parentElement.parentElement.firstElementChild;
                if (nextTab) nextTab.firstElementChild.focus();
            } else {
                const previousTab = currentTab.parentElement.previousElementSibling ?? currentTab.parentElement.parentElement.lastElementChild;
                if (previousTab) previousTab.firstElementChild.focus();
            }
        },
    },
}
</script>