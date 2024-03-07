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
                <table class="table m-0 border-top-0"
                    style="border-top: 0"
                    v-if="this.users.length"
                    v-permission="'achievement_access'"
                >
                    <thead class=" border-top-0">
                        <tr class="border-top-0">
                            <th class="border-top-0">{{trans('global.name')}}</th>
                            <!-- <th class="border-top-0">{{trans('global.notes')}}</th> -->
                            <th class="border-top-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users">
                            <td>{{ user.firstname }} {{ user.lastname }}</td>
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
                                    :settings="{'achievements' : false, 'edit': false}">
                                </AchievementIndicator>
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
    },
    components: {
        AchievementIndicator
    }
}
</script>