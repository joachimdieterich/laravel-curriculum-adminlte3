<template>
    <div
        id="header"
        class="d-flex position-sticky bg-lime user-select-none"
        style="min-height: 50px; top: 0;"
    >
        <div id="brand-menu"
            class="d-flex position-relative mr-2"
        >
            <button
                id="brand-menu-dropdown-button"
                class="btn"
                type="button"
                @click.stop="toggleBrandMenu()"
            >
                <span
                    class="d-flex align-items-center px-3 px-sm-4"
                    style="gap: 10px;"
                >
                    <svg
                        version="1.0" xmlns="http://www.w3.org/2000/svg"
                        height="40px" viewBox="0 0 400.000000 460.000000"
                        preserveAspectRatio="xMidYMid meet"
                    >
                        <g transform="translate(0.000000,460.000000) scale(0.100000,-0.100000)" fill="#fff" stroke="none">
                            <path d="M2231 4531 c-8 -5 -48 -74 -90 -153 l-76 -143 -60 -13 c-153 -32
                                -325 -76 -380 -96 l-60 -21 -135 87 c-156 102 -173 105 -259 57 -201 -114
                                -482 -343 -498 -407 -3 -14 11 -78 36 -161 23 -75 43 -146 43 -157 1 -11 -28
                                -65 -64 -119 -36 -55 -94 -152 -128 -215 -34 -63 -65 -119 -70 -123 -4 -5 -70
                                -22 -146 -38 -177 -37 -189 -42 -207 -87 -23 -59 -55 -207 -71 -332 -19 -145
                                -30 -352 -19 -366 4 -6 71 -43 148 -83 77 -40 145 -78 151 -84 6 -7 22 -73 37
                                -147 14 -74 41 -182 60 -240 18 -59 30 -110 25 -115 -5 -6 -47 -68 -93 -140
                                -61 -93 -85 -139 -85 -161 0 -65 203 -365 350 -516 69 -72 78 -78 115 -78 22
                                0 90 15 150 34 177 54 158 55 262 -13 50 -34 140 -87 200 -120 157 -86 149
                                -76 178 -220 40 -196 40 -197 83 -214 52 -21 250 -64 351 -76 119 -15 354 -24
                                367 -14 7 4 46 71 88 148 42 77 83 145 92 151 8 6 38 15 67 18 66 9 228 49
                                334 83 l82 27 138 -92 c76 -51 149 -92 162 -92 77 0 530 330 576 421 23 43 18
                                91 -21 221 -19 66 -36 127 -36 136 -1 9 24 55 55 101 55 82 69 126 49 157 -9
                                15 -406 254 -421 254 -6 0 -50 23 -98 50 -104 60 -134 63 -171 18 -57 -67
                                -256 -255 -315 -296 -409 -287 -958 -268 -1349 47 -267 216 -405 461 -439 786
                                -37 344 94 685 360 938 346 329 831 410 1270 210 71 -32 123 -41 147 -25 7 4
                                59 84 116 177 247 402 264 435 240 471 -4 6 -55 37 -114 68 -59 32 -109 65
                                -112 74 -3 9 -19 78 -34 152 -16 75 -34 146 -41 157 -20 39 -182 79 -436 108
                                -145 17 -283 19 -304 6z"
                            />
                        </g>
                    </svg>
                    <span class="d-none d-sm-inline">{{ trans(brandMenuText) }}</span>
                    <i v-if="notLocalEnv"
                        class="fa fa-chevron-down"
                        style="font-size: 0.75rem;"
                    ></i>
                </span>
            </button>
            <div v-if="notLocalEnv"
                id="brand-menu-dropdown"
                class="d-none bg-lime w-100 mr-2"
            >
                <div v-for="entry in menu">
                    <a :href="entry.url">
                        <i class="fa fa-book text-white text-center" :class="entry.icon"></i>
                        <span class="ml-2 my-1">
                            {{ entry.title ?? ';asdf asdf sda'}}
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center position-relative">
            <a
                href="#content"
                class="skip bg-lime font-weight-bold position-absolute"
                style="white-space: nowrap; z-index: 1040;"
            >
                {{ trans('global.skip_navigation') }}
            </a>
        </div>

        <div class="d-flex align-items-center">
            <button v-if="checkPermission('is_admin')"
                type="button"
                class="btn btn-icon text-dark"
                data-toggle="collapse"
                data-target=".nav-collapse"
                aria-expanded="false"
                aria-controls="navigationbar background-mask"
            >
                <i class="fa fa-bars px-1"></i>
            </button>
            <a
                class="text-dark text-decoration-none p-md-2 mx-2"
                href="/"
            >
                <button class="d-md-none btn btn-icon bg-lime-accent">
                    <i class="fa fa-home"></i>
                </button>
                <strong class="d-none d-md-inline">{{ trans('global.home') }}</strong>
            </a>
            <Searchbar/>
        </div>

        <div
            id="user-menu"
            class="d-flex align-items-center ml-auto"
        >
            <button
                id="user-menu-dropdown-button"
                class="btn"
                type="button"
                @click.stop="toggleUserMenu()"
            >
                <span v-if="isGuestUser">
                    <i class="fa fa-right-to-bracket"></i>
                    <strong class="py-1 ml-2">{{ trans('global.login') }}</strong>
                </span>
                <span v-else
                    class="d-flex align-items-center pointer"
                >
                    <Avatar
                        :username="user.username"
                        :firstname="user.firstname"
                        :lastname="user.lastname"
                        :size="40"
                        :medium_id="user.medium_id"
                    />
                    <span
                        class="d-none d-md-inline ml-1"
                        style="font-weight: 900;"
                    >{{ user.firstname }} {{ user.lastname }}</span>
                </span>
            </button>
            <div v-if="!isGuestUser"
                id="user-menu-dropdown"
                class="d-none bg-lime"
                style="right: 0.5rem; width: 250px;"
            >
                <div
                    class="d-flex justify-content-center py-2"
                    style="border-bottom: 1px solid white"
                >
                    <strong class="text-black">{{ role.title }}</strong>
                </div>

                <div>
                    <a :href="'/users/' + user.id">
                        <i class="fa fa-id-card mr-2 fa-fw text-white"></i>
                        {{ trans('global.myProfile') }}
                    </a>
                </div>
                <div v-if="checkPermission('note_access')">
                    <a href="/notes">
                        <i class="fa fa-sticky-note fa-fw mr-2 text-white"></i>
                        {{ trans('global.note.title') }}
                    </a>
                </div>
                <div v-if="role.id === 1">
                    <a href="/admin">
                        <i class="fa fa-cogs fa-fw mr-2 text-white"></i>
                        {{ trans('global.config.title') }}
                    </a>
                </div>
                <div>
                    <a href="#" @click.prevent="logout()">
                        <i class="fa fa-power-off fa-fw mr-2 text-white"></i>
                        {{ trans('global.logout') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Searchbar from './Searchbar.vue';
import Avatar from './Avatar.vue';

export default {
    name: 'Header',
    components: {
        Searchbar,
        Avatar,
    },
    props: {
        menu: {
            type: Object,
            required: true,
            title: 'entries to link to other ressources',
        },
        env: {
            type: String,
            required: true,
        },
        user: {
            type: Object,
            required: true,
        },
        role: {
            type: Object,
            required: true,
        },
        guestId: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            brandMenuVisible: false,
            userMenuVisible: false,
        }
    },
    methods: {
        toggleBrandMenu() {
            if (!this.notLocalEnv) return window.location.href = '/home';

            document.getElementById('brand-menu-dropdown').classList.toggle('d-none');

            if (this.brandMenuVisible = !this.brandMenuVisible) {
                document.addEventListener('click', this.listenToBrandMenu);
            } else {
                document.removeEventListener('click', this.listenToBrandMenu);
            }
        },
        toggleUserMenu() {
            if (this.isGuestUser) return this.logout();

            document.getElementById('user-menu-dropdown').classList.toggle('d-none');

            if (this.userMenuVisible = !this.userMenuVisible) {
                document.addEventListener('click', this.listenToUserMenu);
            } else {
                document.removeEventListener('click', this.listenToUserMenu);
            }
        },
        logout() {
            document.getElementById('logoutform').submit();
        },
        listenToBrandMenu(e) {
            const dropdownElem = document.getElementById('brand-menu-dropdown');
            if (!dropdownElem.contains(e.target)) {
                this.brandMenuVisible = false;
                dropdownElem.classList.add('d-none');
                document.removeEventListener('click', this.listenToBrandMenu);
            }
        },
        listenToUserMenu(e) {
            const dropdownElem = document.getElementById('user-menu-dropdown');
            if (!dropdownElem.contains(e.target)) {
                this.userMenuVisible = false;
                dropdownElem.classList.add('d-none');
                document.removeEventListener('click', this.listenToUserMenu);
            }
        },
    },
    computed: {
        notLocalEnv() {
            return this.env !== 'local';
        },
        brandMenuText() {
            const href = window.location.pathname;

            if (href.startsWith('/kanbans')) return 'global.kanban.title'
            else if (href.startsWith('/videoconferences')) return 'global.videoconference.title'
            else return 'Curriculum';
        },
        isGuestUser() {
            return this.user.id === this.guestId;
        },
    },
}
</script>