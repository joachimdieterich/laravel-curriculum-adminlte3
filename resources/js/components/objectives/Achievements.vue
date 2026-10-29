<template>
    <div>
        <table v-if="!isTeacher && checkPermission('achievement_create_self_assessment')"
            class="table m-0 border-top-0"
        >
            <thead class="border-top-0">
                <tr class="border-top-0">
                    <th class="border-top-0">{{ trans('global.name') }}</th>
                    <th class="border-top-0">{{ trans('global.created_at') }}</th>
                    <th class="border-top-0">{{ trans('global.updated_at') }}</th>
                    <th class="border-top-0">{{ trans('global.teacher') }}</th>
                    <th class="border-top-0">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td v-if="objective.achievements[0]">
                        {{ objective.achievements[0].user.firstname }} {{ objective.achievements[0].user.lastname }}
                    </td>
                    <td v-else></td>
                    <td v-if="objective.achievements[0]">
                        {{ objective.achievements[0].created_at }}
                    </td>
                    <td v-else></td>
                    <td v-if="objective.achievements[0]">
                        {{ objective.achievements[0].updated_at }}
                    </td>
                    <td v-else></td>
                    <td v-if="objective.achievements[0]">
                        {{ objective.achievements[0].owner.firstname }} {{ objective.achievements[0].owner.lastname }}
                    </td>
                    <td v-else></td>
                    <td>
                        <AchievementIndicator v-if="objective.achievements[0]"
                            :objective="objective"
                            :type="type"
                            :users="[objective.achievements[0].user.id]"
                            :settings="{'achievements' : false, 'edit': false}"
                        />
                        <AchievementIndicator v-else
                            :objective="objective"
                            :type="type"
                            :settings="{'achievements' : false, 'edit': false}"
                        />
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="isTeacher"
            class="form-group p-2"
        >
            <Select2
                id="organization_type_id"
                name="organization_type_id"
                url="/groups"
                model="group"
                @selectedValue="id => selectGroup(id)"
            />
        </div>

        <table v-if="this.users.length && isTeacher"
            class="table m-0 border-top-0"
        >
            <thead class="border-top-0">
                <tr class="border-top-0">
                    <th class="border-top-0">{{ trans('global.name') }}</th>
                    <th class="border-top-0">{{ trans('global.created_at') }}</th>
                    <th class="border-top-0">{{ trans('global.updated_at') }}</th>
                    <th class="border-top-0">{{ trans('global.teacher') }}</th>
                    <th class="border-top-0">{{ trans('global.notes') }}</th>
                    <th class="border-top-0">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users">
                    <td>{{ user.firstname }} {{ user.lastname }}</td>
                    <td>
                        <span v-if="currentUser(user.id).achievements[0]">
                            {{ currentUserObjective.achievements[0].created_at }}
                        </span>
                    </td>
                    <td>
                        <span v-if="currentUserObjective.achievements[0]">
                            {{ currentUserObjective.achievements[0].updated_at }}
                        </span>
                    </td>
                    <td>
                        <span v-if="currentUserObjective.achievements[0]">
                            {{ currentUserObjective.achievements[0].owner.firstname }} {{ currentUser(user.id).achievements[0].owner.lastname }}
                        </span>
                    </td>
                    <td v-if="currentUserObjective.achievements[0]">
                        <i
                            class="fa fa-sticky-note text-muted pointer"
                            style="font-size: 18px;"
                            @click.prevent="show(currentUserObjective.achievements[0].id)"
                        ></i>
                    </td>
                    <td v-else></td>
                    <td>
                        <AchievementIndicator
                            :objective="currentUserObjective"
                            :type="type"
                            :users="[user.id]"
                            :settings="{'achievements' : false, 'edit': false}"
                        />
                    </td>
                </tr>
            </tbody>
        </table>
        <Teleport to="body">
            <NoteModal/>
        </Teleport>
    </div>
</template>
<script>
import AchievementIndicator from './AchievementIndicator.vue';
import NoteModal from "../note/NoteModal.vue";
import Select2 from "../forms/Select2.vue";
import GradeModal from "../grade/GradeModal.vue";

export default {
    components: {
        GradeModal,
        Select2,
        AchievementIndicator,
        NoteModal,
    },
    props: {
        objective: {},
        type: {},
    },
    data() {
        return {
            objectiveWithAchievements : {},
            users: {},
            currentUserObjective: null,
            selectedGroup: null,
            noteParams: null,
        }
    },
    methods: {
        show(user_id) {
            this.globalStore?.showModal('note-modal', {
                notable_type: 'App\\Achievement',
                notable_id: user_id,
                show_tabs: false,
            });
        },
        loaderEvent() {
            axios.get('/enablingObjectives/' + this.objective.id + '/achievements/' + this.selectedGroup)
                .then(response => {
                    this.objectiveWithAchievements  = response.data.objective;
                    this.users                      = response.data.users;
                }).catch(e => console.log(e));
        },
        currentUser(id) {
            const achievement = this.objectiveWithAchievements.achievements.find(e => e.user_id == id);
            let currentUsersObjective = {...this.objectiveWithAchievements};
            currentUsersObjective.achievements = [achievement];
            this.currentUserObjective = currentUsersObjective;

            return currentUsersObjective;
        },
        selectGroup(id) {
            this.selectedGroup = id;
            this.loaderEvent();
        }
    },
    computed: {
        isTeacher() {
            return this.checkPermission('is_teacher');
        },
    },
}
</script>