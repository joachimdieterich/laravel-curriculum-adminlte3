<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ trans('global.plan.select_users') }}
                    </h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <table
                        v-permission="'achievement_access'"
                        id="achievements-table"
                        class="table m-0 border-top-0"
                        style="border-top: 0"
                    >
                        <thead class="border-top-0">
                            <tr class="border-0">
                                <th class="border-0" style="width: 0px;"></th>
                                <th
                                    id="firstname"
                                    class="border-0 pointer"
                                    @click="sortBy(1)"
                                >
                                    {{ trans('global.firstname') }}
                                    <i class="fa fa-sort-down ml-1"></i>
                                </th>
                                <th
                                    class="border-0 pointer"
                                    @click="sortBy(2)"
                                >
                                    {{ trans('global.lastname') }}
                                    <i class="fa fa-sort text-gray ml-1"></i>
                                </th>
                                <!-- <th class="border-top-0">{{trans('global.notes')}}</th> -->
                                <th class="border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-width: 3px 0px; border-style: solid; border-color: #dee2e6;">
                                <td>
                                    <input
                                        type="checkbox"
                                        v-model="checkAll"
                                        @change="toggleUsers()"
                                    />
                                </td>
                                <td colspan="2">
                                    {{ selectedUsers.length  + ' ' + trans('global.users_selected')}}
                                </td>
                                <td>
                                    <AchievementIndicator
                                        v-permission="'achievement_create'"
                                        class="mr-3"
                                        :objective="objective.default"
                                        :type="'enabling'"
                                        :users="selectedUsers"
                                        :settings="{
                                            achievements : false,
                                            edit: false,
                                            referenceable_id: referenceable_id,
                                            referenceable_type: referenceable_type,
                                        }"
                                        :disabled="selectedUsers.length === 0"
                                    />
                                </td>
                            </tr>
                            <tr v-for="user in users">
                                <td>
                                    <input
                                        type="checkbox"
                                        :value="user.id"
                                        v-model="selectedUsers"
                                    />
                                </td>
                                <td class="pr-2">{{ user.firstname }}</td>
                                <td class="pr-2">{{ user.lastname }}</td>
                                <!-- <td v-if="currentUser(user.id).achievements[0]">
                                    <i style="font-size:18px;"
                                        class="far fa-sticky-note text-muted pointer"
                                        @click.prevent="$modal.show('note-modal', {'method': 'post', 'notable_type': 'App\\Achievement', 'notable_id': currentUser(user.id).achievements[0].id,'show_tabs': false}) ">
                                    </i>
                                </td>
                                <td v-else></td> -->
                                <td>
                                    <AchievementIndicator
                                        v-permission="'achievement_create'"
                                        :objective="objective[user.id] ?? objective.default"
                                        :type="'enabling'"
                                        :users="[user.id]"
                                        :settings="{
                                            achievements : false,
                                            edit: false,
                                            referenceable_id: referenceable_id,
                                            referenceable_type: referenceable_type,
                                        }"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="certificate-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="certificate-save"
                            class="btn btn-primary ml-3"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import AchievementIndicator from './../objectives/AchievementIndicator.vue';
import {useGlobalStore} from "../../store/global";

export default {
    name: 'set-achievements-modal',
    components: {
        AchievementIndicator,
    },
    props: {
        users: {},
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            objective: {},
            referenceable_id: null,
            referenceable_type: null,
            search: '',
            checkAll: false,
            selectedUsers: [],
            sortByAsc: true, // on default sorts by firstname A-Z
        }
    },
    methods: {
        toggleUsers() {
            this.selectedUsers = this.checkAll
                ? this.users.map(user => user.id)
                : [];
        },
        // columnNr is index-based => starting at 0
        sortBy(columnNr) {
            this.sortByAsc = !this.sortByAsc;

            const table = document.querySelector('#achievements-table tbody');
            const trElems = table.querySelectorAll('tr:not(:nth-child(1))'); // first tr is the group-selection row

            Array.from(trElems)
                .sort((a, b) => {
                    // TODO: if two rows have the same value, further sort by other column
                    // switch (columnNr) {
                        // TODO: 'Status'-sort needs overhaul
                        // case 4: // column 4 = 'Status'
                        //     return this.sortByAsc
                        //         ? b.cells[columnNr].getAttribute('data-value') - a.cells[columnNr].getAttribute('data-value')
                        //         : a.cells[columnNr].getAttribute('data-value') - b.cells[columnNr].getAttribute('data-value');
                        // case 1: // column 1 = 'Firstname' => default
                        // case 2: // column 2 = 'Lastname'
                        // default:
                        return this.sortByAsc
                            ? a.cells[columnNr].innerText.localeCompare(b.cells[columnNr].innerText) // A-Z
                            : a.cells[columnNr].innerText.localeCompare(b.cells[columnNr].innerText) * -1; // Z-A
                    // }
                }).forEach(tr => table.appendChild(tr));

            // reset sort-icon of previous element
            let previousSortElem = this.$el.querySelector('.fa-sort-up, .fa-sort-down');
            previousSortElem?.classList.add('fa-sort', 'text-gray');
            previousSortElem?.classList.remove('fa-sort-up', 'fa-sort-down');

            // show current sorting indicator
            const th = document.querySelector('#achievements-table').firstChild.firstChild.children[columnNr].children[0];
            const currentSort = this.sortByAsc ? 'fa-sort-down' : 'fa-sort-up';
            th.classList.add(currentSort);
            th.classList.remove('fa-sort', 'text-gray');
        }
    },
    watch: {
        selectedUsers: function() {
            this.checkAll = this.selectedUsers.length === this.users.length;
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;

                this.referenceable_id = params.referenceable_id;
                this.referenceable_type = params.referenceable_type;

                const eventObj = params.objective;
                const obj = {}; // if we add attributes directly to 'this.objective' it won't work
                obj.default = { id: eventObj.id };

                eventObj.achievements.forEach(achievement => {
                    // only add attributes that are actually needed
                    obj[achievement.user_id] = {
                        id: eventObj.id,
                        achievements: [achievement],
                    };
                });

                Object.assign(this.objective, obj);
                this.sortByAsc = true;
            }
        });
    },
}
</script>