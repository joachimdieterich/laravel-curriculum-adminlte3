<template>
    <modal
        id="set-achievements-modal"
        name="set-achievements-modal"
        height="60%"
        :minWidth="300"
        :minHeight="420"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened()"
        @before-close="beforeClose()"
        style="z-index: 1100"
    >
        <div
            class="card"
            style="margin-bottom: 0px !important; height: 100%;"
        >
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.plan.select_users') }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                 </div>
            </div>
            <div class="card-body overflow-auto">
                <div class="d-flex align-items-center" style="padding: 0px 10px;">
                    <span class="flex-fill">
                        {{ selectedUsers.length }} Benutzer ausgewählt
                    </span>
                    <span class="flex-fill">
                        <i
                            class="far fa-sticky-note text-muted p-1"
                            :class="selectedUsers.length === 0 ? 'text-gray' : 'pointer'"
                            style="font-size: 18px;"
                            @click.prevent="openSelectedNotes()"
                        ></i>
                    </span>
                    <AchievementIndicator
                        class="mr-3"
                        v-permission="'achievement_create'"
                        :objective="objective.default"
                        :type="'enabling'"
                        :users="selectedUsers"
                        :settings="{'achievements' : false, 'edit': false, 'sendStatus': true}"
                        :disabled="selectedUsers.length === 0"
                    ></AchievementIndicator>
                </div>
                <table class="table m-0 border-top-0"
                    style="border-top: 0"
                    v-if="this.users.length"
                    v-permission="'achievement_access'"
                >
                    <thead class=" border-top-0">
                        <tr class="border-top-0">
                            <th class="border-top-0" style="width: 0px;">
                                <input
                                    type="checkbox"
                                    v-model="checkAll"
                                    @change="toggleUsers()"
                                />
                            </th>
                            <th class="border-top-0">{{ trans('global.name') }}</th>
                            <th class="border-top-0">{{ trans('global.notes') }}</th>
                            <th class="border-top-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users">
                            <td>
                                <input
                                    type="checkbox"
                                    :value="user.id"
                                    v-model="selectedUsers"
                                />
                            </td>
                            <td>{{ user.firstname }} {{ user.lastname }}</td>
                            <td>
                                <i style="font-size:18px; margin: -0.25rem" 
                                    class="far fa-sticky-note text-muted pointer p-1"
                                    @click.prevent="openNotes(user.id)"
                                ></i>
                            </td>
                            <td>
                                <AchievementIndicator
                                    v-permission="'achievement_create'"
                                    :objective="objective[user.id] ?? objective.default"
                                    :type="'enabling'"
                                    :users="[user.id]"
                                    :settings="{'achievements' : false, 'edit': false}"
                                ></AchievementIndicator>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <span class="d-flex justify-content-between">
                    <button type="button" class="btn btn-default" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                    <button class="btn btn-primary" @click="submit()">{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>
<script>
const AchievementIndicator = () => import('./../objectives/AchievementIndicator.vue');

export default {
    props: {
        users: [],
    },
    data() {
        return {
            objective: {},
            selectedUsers: [],
            checkAll: false,
        };
    },
    mounted() {},
    methods: {
        close() {
            this.$modal.hide('set-achievements-modal');
            this.objective = {};
        },
        submit() {
            this.close();
        },
        /**
         * since we need the achievement-status combined with the specific user,
         * we'll create an object with the user_id as the attribute
         * and set a default for users with no achievement
         * INFO: I don't want to do a double for-loop because O(n^2)
         */
        beforeOpen(event) {
            const eventObj = event.params.objective;
            const obj = {}; // if we add attributes directly to 'this.objective' it won't work
            obj.default = { id: eventObj.id };
            
            eventObj.achievements.forEach(achievement => {
                // only add attributes that are actually needed
                obj[achievement.user_id] = {
                    id: eventObj.id,
                    achievements: [achievement],
                };
            });

            this.objective = obj;
        },
        beforeClose() {},
        opened() {},
        closed() {},
        /**
         * creates a new achievement with unset status </br>
         * if achievement already exists, its status will be set back to default = '00'
         * @param {Number} user_id 
         * @returns {Number} id of newly created achievement
         */
        async createAchievement(user_id) {
            const objective_id = this.objective.default.id;
            const achievement = {
                'referenceable_type': 'App\\EnablingObjective',
                'referenceable_id': objective_id,
                'user_id': user_id,
                'status': '0' // only one zero, setting '00' will throw 500
            }
            // create a new achievement-entry and get the ID
            const achievement_id = (await axios.post('/achievements', achievement)).data.id;

            // add new achievement to data, in case it is needed
            this.objective[user_id] = {
                achievements: [{ id: achievement_id, status: '00' }],
                id: objective_id,
            };

            return achievement_id;
        },
        /**
         * when opening the note-modal, an achievement-id is needed to create a note
         * if there's no achievement, create one with unset status and get a new ID
         * @param {Number} user_id 
         */
        openNotes(user_id) {
            let achievement_id = this.objective[user_id]?.achievements[0].id; // check if achievement exists
            
            if (achievement_id === undefined) {
                achievement_id = this.createAchievement(user_id);
            }

            this.$modal.show('note-modal', {
                'method': 'post',
                'notable_type': 'App\\Achievement',
                'notable_id': achievement_id,
                'show_tabs': false,
            });
        },
        /**
         * get achievement for every selected user or create one if not exists
         */
        async openSelectedNotes() {
            // gets an object-structure like this { user_id: achievement_id | undefined }
            let achievements = {};
            this.selectedUsers.forEach(
                user_id => achievements[user_id] = this.objective[user_id]?.achievements[0].id
            );

            for (const [user_id, value] of Object.entries(achievements)) {
                if (value === undefined) {
                    // changes value from undefined to achievement_id
                    achievements[user_id] = this.createAchievement(user_id);
                }
            }

            this.$modal.show('note-modal', {
                'method': 'post',
                'notable_type': 'App\\Achievement',
                'notable_id': Object.values(achievements), // array of achievement_ids
                'show_tabs': false,
            });
        },
        /**
         * function is called in AchievementIndicator.vue to overwrite current statuses
         * @param {string} status the second char of status
         * @param {object} users list of user-ids and their achievement-id
         */
        updateStatus(status, users) {
            for (const [user_id, achievement_id] of Object.entries(users)) {
                if (this.objective[user_id] === undefined) {
                    this.objective[user_id] = {
                        achievements: [{ id: achievement_id, status: '00' }],
                        id: this.objective.default.id,
                    };
                }
                
                const oldVal = this.objective[id].achievements[0].status;
                // only overwrite the second char of the status
                this.objective[id].achievements[0].status = oldVal[0] + status;
            }
        },
        toggleUsers() {
            this.selectedUsers = this.checkAll
                ? this.users.map(user => user.id)
                : [];
        },
    },
    components: {
        AchievementIndicator
    }
}
</script>