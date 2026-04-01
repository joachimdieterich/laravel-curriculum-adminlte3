<template>
    <div
        id="navigationbar"
        class="position-absolute bg-white collapse nav-collapse width h-100"
    >
        <div id="nav-wrapper">
            <div v-for="tab in tabs"
                class="nav-group d-flex flex-column py-2"
            >
                <div v-if="tab.title"
                    class="nav-group-title"
                >
                    <strong>{{ trans(tab.title) }}</strong>
                </div>
                <div v-for="entry in tab.entries"
                    class="nav-entry"
                >
                    <a
                        :href="entry.href"
                        class="d-flex align-items-center px-3 py-2 text-dark text-decoration-none"
                        :class="{ 'active': entry.href == activeEntry }"
                    >
                        <i class="fa text-center" :class="entry.icon"></i>
                        {{ trans('global.' + entry.model + '.title') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "Navigationbar",
    data() {
        return {
            tabs: [
                {
                    title: null,
                    entries: [
                        {
                            model: 'curriculum',
                            icon: 'fa-th',
                            href: '/curricula',
                        },
                        {
                            model: 'logbook',
                            icon: 'fa-book',
                            href: '/logbooks',
                        },
                        {
                            model: 'plan',
                            icon: 'fa-clipboard',
                            href: '/plans',
                        },
                        {
                            model: 'kanban',
                            icon: 'fa-columns',
                            href: '/kanbans',
                        },
                        {
                            model: 'task',
                            icon: 'fa-tasks',
                            href: '/tasks',
                        },
                        {
                            model: 'map',
                            icon: 'fa-map-location-dot',
                            href: '/maps',
                        },
                        {
                            model: 'exam',
                            icon: 'fa-ranking-star',
                            href: '/exams',
                        },
                        {
                            model: 'videoconference',
                            icon: 'fa-video',
                            href: '/videoconferences',
                        },
                    ],
                },
                {
                    title: 'global.user_management',
                    entries: [
                        {
                            model: 'permission',
                            icon: 'fa-unlock',
                            href: '/permissions',
                        },
                        {
                            model: 'role',
                            icon: 'fa-user-tag',
                            href: '/roles',
                        },
                        {
                            model: 'user',
                            icon: 'fa-user',
                            href: '/users',
                        },
                    ],
                },
                {
                    title: 'global.organization_management',
                    entries: [
                        {
                            model: 'navigator',
                            icon: 'fa-map-signs',
                            href: '/navigators',
                        },
                        {
                            model: 'group',
                            icon: 'fa-users',
                            href: '/groups',
                        },
                        {
                            model: 'grade',
                            icon: 'fa-layer-group',
                            href: '/grades',
                        },
                        {
                            model: 'period',
                            icon: 'fa-history',
                            href: '/periods',
                        },
                        {
                            model: 'subject',
                            icon: 'fa-swatchbook',
                            href: '/subjects',
                        },
                        {
                            model: 'organizationType',
                            icon: 'fa-city',
                            href: '/organizationTypes',
                        },
                        {
                            model: 'certificate',
                            icon: 'fa-certificate',
                            href: '/certificates',
                        },
                        {
                            model: 'organization',
                            icon: 'fa-university',
                            href: '/organizations',
                        },
                    ],
                },
                {
                    title: 'global.system_config.title',
                    entries: [
                        {
                            model: 'tag',
                            icon: 'fa-tag',
                            href: '/tags',
                        },
                    ],
                },
            ],
            activeEntry: null,
            hidden: true,
        }
    },
    mounted() {
        this.activeEntry = window.location.pathname.match(/\/[^\/]*/)[0];
    },
}
</script>
<style>
#navigationbar {
    z-index: 10;
    
    & > #nav-wrapper {
        width: 300px;
        height: 100%;
        padding: 10px;
        overflow-y: auto;
        background-color: white;
    }
    & .nav-group { gap: 5px; }
    & .nav-entry {
        font-size: 1.125rem;
        white-space: nowrap;
        
        & a {
            gap: 5px;
            border-radius: 10px;
            transition: background-color 0.2s ease;

            &:hover:not(.active), &:focus-visible:not(.active) {
                background-color: #e5e5e5;
            }
            &.active {
                background-color: #007bff;
                color: white !important;
            }
            & > .fa {
                width: 24px;
                max-height: 24px;
                font-size: 1.25rem;
            }
        }
    }
}
/* element is placed in master.blade */
#background-mask {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 0 !important;
    background-color: transparent;
    z-index: 5;
    transition: background-color 0.3s ease;

    &.collapsing, &.show {
        height: 100% !important;
    }
    &.show { background-color: rgba(0, 0, 0, 0.25); }
}
</style>