<template>
    <div
        id="home"
        class="d-flex flex-column flex-md-row mx-lg-3"
    >
        <div
            id="home-left"
            class="col-12 col-md-6 flex-shrink-1 px-3"
        >
            <InfoBox
                model="courses"
                :text="trans('global.curriculum.title')"
                icon="fa-th"
                icon-background-class="bg-cyan"
                :hide-if-empty="true"
                href="/curricula"
                @error="handleError"
            >
                <template #entry="{ entry }">
                    <a :href="'/courses/' + entry.course_id">
                        <span class="font-weight-bold">{{ entry.title }}</span>
                        <span v-if="isVisible.progress"
                            class="pull-right w-50"
                        >
                            <ProgressBar
                                :achievements="entry.achievements"
                                :maxEntries="entry.enabling_objectives_count"
                            />
                        </span>
                        <br/>
                        <span class="text-muted">{{ entry.group_title }}</span>
                    </a>
                </template>
            </InfoBox>

            <InfoBox v-if="isVisible.groups"
                model="groups"
                :text="trans('global.group.title')"
                icon="fa-users"
                icon-background-class="bg-purple"
                @error="handleError"
            />

            <InfoBox v-if="isVisible.achievements"
                model="achievements"
                :disable-link="true"
                :text="trans('global.achievement.recent')"
                icon="fa-trophy"
                icon-background-class="bg-blue"
                :hide-if-empty="true"
                @error="handleError"
            >
                <template #entry="{ entry }">
                    <span
                        class="d-flex flex-lg-nowrap align-items-center"
                        :class="entry.history.length > 0 && 'flex-wrap'"
                        style="gap: 0.5rem;"
                    >
                        <span v-if="entry.history.length > 0"
                            class="position-relative d-flex align-items-center"
                        >
                            <i
                                class="text-green t-20 me-1"
                                :class="iconClass(entry.history[0].status, 1)"
                            ></i>
                            <i
                                class="text-orange t-20 me-1"
                                :class="iconClass(entry.history[0].status, 2)"
                            ></i>
                            <i
                                class="text-red t-20 me-1"
                                :class="iconClass(entry.history[0].status, 3)"
                            ></i>
                            <i class="fa-solid fa-arrow-right-long ms-1"></i>
                        </span>
                        <span class="d-flex">
                            <i
                                class="text-green t-20 me-1"
                                :class="iconClass(entry.status, 1)"
                            ></i>
                            <i
                                class="text-orange t-20 me-1"
                                :class="iconClass(entry.status, 2)"
                            ></i>
                            <i
                                class="text-red t-20"
                                :class="iconClass(entry.status, 3)"
                            ></i>
                        </span>
                        <span
                            class="p-margin-0 m-0"
                            v-html="entry.referenceable.title"
                        ></span>
                    </span>
                </template>
            </InfoBox>
        </div>

        <div
            id="home-right"
            class="col-12 col-md-6 flex-shrink-1 px-3"
        >
            <InfoBox
                model="logbooks"
                :text="trans('global.logbook.title')"
                icon="fa-book"
                icon-background-class="bg-red"    
                :has-modal="true"
                @open-modal="openModal('logbook-modal')"
                @error="handleError"
            />

            <InfoBox
                model="kanbans"
                :text="trans('global.kanban.title')"
                icon="fa-columns"
                icon-background-class="bg-yellow"
                :has-modal="true"
                @open-modal="openModal('kanban-modal')"
                @error="handleError"
            />
    
            <InfoBox
                model="plans"
                :text="trans('global.plan.title')"
                icon="fa-clipboard-list"
                icon-background-class="bg-green"
                :hide-if-empty="!isVisible.plans"
                :has-modal="isVisible.plans"
                @open-modal="openModal('plan-modal')"
                @error="handleError"
            />

            <InfoBox v-if="isVisible.users"
                model="users"
                :text="trans('global.user_management')"
                icon="fa-user"
                icon-background-class="bg-blue"
                :header-only="true"
            />

            <InfoBox
                model="exams"
                :disable-link="true"
                :text="trans('global.exam.title')"
                icon="fa-ranking-star"
                icon-background-class="bg-maroon"
                :hide-if-empty="!isVisible.exams"
                :has-modal="isVisible.exams"
                @open-modal="openModal('subscribe-exam-modal')"
                @error="handleError"
            >
                <template #entry="{ entry }">
                    <a :href="entry.login_url ?? '/exams/' + entry.exam_id + '/edit'">
                        <span class="font-weight-bold">
                            {{ entry.test_name }}
                        </span>
                        <span class="link-muted text-decoration-none">
                            ({{ entry.group.title }})
                        </span>
                    </a>
                </template>
            </InfoBox>
        </div>
        <LogbookModal/>
        <KanbanModal/>
        <PlanModal/>
        <MediumModal/>
        <SubscribeExamModal/>
    </div>
</template>
<script>
import InfoBox from '../uiElements/InfoBox.vue';
import ProgressBar from '../uiElements/ProgressBar.vue';
import LogbookModal from '../logbook/LogbookModal.vue';
import KanbanModal from '../kanban/KanbanModal.vue';
import PlanModal from '../plan/PlanModal.vue';
import SubscribeExamModal from '../exam/SubscribeExamModal.vue';
import MediumModal from '../media/MediumModal.vue';

export default {
    name: 'Home',
    mounted() {
        this.$eventHub.on('logbook-added', (logbook) => {
            window.location.href = '/logbooks/' + logbook.id;
        });
        this.$eventHub.on('kanban-added', (kanban) => {
            window.location.href = '/kanbans/' + kanban.id;
        });
        this.$eventHub.on('plan-added', (plan) => {
            window.location.href = '/plans/' + plan.id;
        });
        this.$eventHub.on('exam-added', (exam) => {
            window.location.href = '/exams/' + exam.exam_id + '/edit';
        });
    },
    methods: {
        openModal(modalName) {
            this.globalStore.showModal(modalName, {});
        },
        handleError(error) {
            this.toast.error(this.errorMessage(error));
        },
        iconClass(status, value) {
            let classes = 'far fa-circle';
            // status can't be '00'
            if (status.charAt(0) === status.charAt(1) && status.charAt(0) == value) {
                classes = 'fa fa-check-circle';
            } else if (status.charAt(0) == value) {
                classes = 'fa fa-circle';
            } else if (status.charAt(1) == value) {
                classes = 'far fa-check-circle';
            }

            return classes;
        },
    },
    computed: {
        isVisible() {
            const isTeacher = this.checkPermission('is_teacher');
            const isAdmin = this.checkPermission('is_admin');

            return {
                progress: !isTeacher || isAdmin,
                groups: isTeacher,
                plans: isTeacher,
                exams: isTeacher,
                achievements: !isTeacher || isAdmin, // for testing purposes, show as admin
                users: isAdmin,
            };
        },
    },
    components: {
        InfoBox,
        ProgressBar,
        LogbookModal,
        KanbanModal,
        PlanModal,
        SubscribeExamModal,
        MediumModal,
    },
}
</script>