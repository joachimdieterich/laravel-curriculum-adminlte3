<template>
    <div class="d-flex flex-column">
        <NavList
            class="px-3"
            :navs="[
                { icon: 'fa-th', target: 'curricula', label: 'global.curriculum.title' },
                { icon: 'fa-users', target: 'users', label: 'global.user.title', permission: 'group_enrolment' },
                { icon: 'fa-book', target: 'logbooks', label: 'global.logbook.title' },
                { icon: 'fa-columns', target: 'kanbans', label: 'global.kanban.title' },
                { icon: 'fa-clipboard-list', target: 'plans', label: 'global.plan.title' },
                { icon: 'fa-ranking-star', target: 'exams', label: 'global.exam.title' },
                { icon: 'fa-video', target: 'videoconferences', label: 'global.videoconference.title' },
            ]"
        />

        <div class="tab-content">
            <!-- 1 Curricula -->
            <div
                id="curricula"
                class="tab-pane fade show active"
                role="tabpanel"
                aria-labelledby="curriculum-tab"
                tabindex=0
            >
                <Courses
                    ref="Courses"
                    :group="group"
                />
            </div>
            <!-- 2 Users -->
            <div v-if="checkPermission('group_enrolment')"
                id="users"
                class="tab-pane fade"
                role="tabpanel"
                aria-labelledby="users-tab"
                tabindex=0
            >
                <Users
                    ref="Users"
                    subscribable_type="App\Group"
                    :subscribable_id="group.id"
                    :subscribable="true"
                />
            </div>
            <!-- 3 Logbooks -->
            <div
                id="logbooks"
                class="tab-pane fade"
                role="tabpanel"
                aria-labelledby="logbooks-tab"
                tabindex=0
            >
                <Logbooks
                    ref="Logbooks"
                    subscribable_type="App\Group"
                    :subscribable_id="group.id"
                    :subscribable="true"
                />
            </div>
            <!-- 4 Kanbans -->
            <div
                id="kanbans"
                class="tab-pane fade"
                role="tabpanel"
                aria-labelledby="kanbans-tab"
                tabindex=0
            >
                <Kanbans
                    ref="Kanbans"
                    subscribable_type="App\Group"
                    :subscribable_id="group.id"
                    :subscribable="true"
                />
            </div>
            <!-- 5 Tasks -->
            <!-- <div
                v-permission="'task_access'"
                id="tasks"
                class="tab-pane fade"
                role="tabpanel"
                aria-labelledby="tasks-tab"
            >
                <Tasks
                    ref="Tasks"
                    subscribable_type="App\Group"
                    :subscribable_id="group.id"
                />
            </div> -->
            <!-- 6 Plans -->
            <div
                id="plans"
                class="tab-pane fade"
                role="tabpanel"
                aria-labelledby="plans-tab"
                tabindex=0
            >
                <Plans
                    ref="Plans"
                    subscribable_type="App\Group"
                    :subscribable_id="group.id"
                    :subscribable="true"
                />
            </div>
            <!-- 7 Exams -->
            <div
                id="exams"
                class="tab-pane fade"
                role="tabpanel"
                aria-labelledby="exams-tab"
                tabindex=0
            >
                <Exams
                    ref="Exams"
                    subscribable_type="App\Group"
                    :subscribable_id="group.id"
                    :subscribable="true"
                />
            </div>
            <!-- 8 Videoconferences -->
            <div
                id="videoconferences"
                class="tab-pane fade"
                role="tabpanel"
                aria-labelledby="videoconferences-tab"
                tabindex=0
            >
                <Videoconferences
                    ref="Videoconference"
                    subscribable_type="App\Group"
                    :subscribable_id="group.id"
                    :subscribable="true"
                />
            </div>
        </div>

        <Teleport to="body">
            <GroupModal/>
        </Teleport>

        <teleport to="#customTitle">
            <div class="d-flex align-items-center">
                <small v-text="currentGroup.title"></small>
                <button v-if="checkPermission('group_edit')"
                    type="button"
                    class="btn btn-icon text-secondary ms-2"
                    data-bs-toggle="tooltip"
                    :data-bs-title="trans('global.group.edit')"
                    @click="editGroup()"
                >
                    <i class="fa fa-pencil"></i>
                </button>
            </div>
        </teleport>
    </div>
</template>
<script>
import NavList from "../uiElements/NavList.vue";
import GroupModal from "../group/GroupModal.vue";
import Videoconferences from "../videoconference/Videoconferences.vue";
import Courses from "../course/Courses.vue";
import Users from "../user/Users.vue";
import Logbooks from "../logbook/Logbooks.vue";
import Kanbans from "../kanban/Kanbans.vue";
//import Tasks from "../task/Tasks.vue";
import Plans from "../plan/Plans.vue";
import Exams from "../exam/Exams.vue";

export default {
    components: {
        NavList,
        GroupModal,
        Videoconferences,
        Users,
        Courses,
        Logbooks,
        Kanbans,
        //Tasks,
        Plans,
        Exams,
    },
    props: {
        group: {
            type: Object,
            default: null,
        },
        courses: Array,
    },
    data() {
        return {
            componentId: this.$.uid,
            currentGroup: {},
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;
        this.globalStore['searchTagModelContext'] = null;

        this.currentGroup = this.group;

        this.enableTooltips();

        this.$eventHub.on('group-updated', group => {
            this.currentGroup = group;
        });
    },
    methods: {
        editGroup() {
            this.globalStore.showModal('group-modal', this.currentGroup);
        },
    },
}
</script>