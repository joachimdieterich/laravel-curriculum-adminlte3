<template>
    <div
        id="home"
        class="d-flex flex-column flex-md-row mx-lg-3"
    >
        <div
            id="home-left"
            class="col-12 col-md-6 flex-shrink-1"
        >
            <InfoBox
                model="courses"
                :text="trans('global.curriculum.title')"
                icon="fa-th"
                icon-background-class="bg-cyan"
                href="/curricula"
                @error="handleError"
            >
                <template #entry="{ entry }">
                    <a :href="'/courses/' + entry.course_id">
                        <span class="font-weight-bold">{{ entry.title }}</span>
                        <span class="pull-right w-50">
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
                :text="trans('global.achievement.recent')"
                icon="fa-trophy"
                icon-background-class="bg-blue"
                @error="handleError"
            >
                <template #entry="{ entry }">
                    <span
                        class="d-flex flex-lg-nowrap align-items-center"
                        :class="entry.history.length > 0 && 'flex-wrap'"
                        style="gap: 0.5rem;"
                    >
                        <span v-if="entry.history.length > 0"
                            class="d-flex align-items-center"
                        >
                            <i
                                class="text-green t-20 mr-1"
                                :class="iconClass(entry.history[0].status, 1)"
                            ></i>
                            <i
                                class="text-orange t-20 mr-1"
                                :class="iconClass(entry.history[0].status, 2)"
                            ></i>
                            <i
                                class="text-red t-20 mr-1"
                                :class="iconClass(entry.history[0].status, 3)"
                            ></i>
                            <i class="fa-solid fa-arrow-right-long ml-1"></i>
                        </span>
                        <span class="d-flex">
                            <i
                                class="text-green t-20 mr-1"
                                :class="iconClass(entry.status, 1)"
                            ></i>
                            <i
                                class="text-orange t-20 mr-1"
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
            class="col-12 col-md-6 flex-shrink-1"
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
    
            <InfoBox v-if="isVisible.plans"
                model="plans"
                :text="trans('global.plan.title')"
                icon="fa-clipboard-list"
                icon-background-class="bg-green"
                :has-modal="isVisible.plans"
                @open-modal="openModal('plan-modal')"
                @error="handleError"
            />

            <InfoBox v-if="checkPermission('is_admin')"
                model="users"
                :text="trans('global.user_management')"
                icon="fa-user"
                icon-background-class="bg-blue"
                :link-only="true"
            />
        </div>
        <LogbookModal/>
        <KanbanModal/>
        <PlanModal/>
        <MediumModal/>
    </div>
</template>
<script>
import InfoBox from '../uiElements/InfoBox.vue';
import ProgressBar from '../uiElements/ProgressBar.vue';
import LogbookModal from '../logbook/LogbookModal.vue';
import KanbanModal from '../kanban/KanbanModal.vue';
import PlanModal from '../plan/PlanModal.vue';
import MediumModal from '../media/MediumModal.vue';
import { useGlobalStore } from '../../store/global';
import { useToast } from 'vue-toastification';

export default {
    name: 'Home',
    setup() {
        const globalStore = useGlobalStore();
        const toast = useToast();
        return {
            globalStore,
            toast,
        };
    },
    mounted() {
        this.$eventHub.on('logbook-added', (logbook) => {
            window.location.href = '/logbooks/' + logbook.id;
        });
        this.$eventHub.on('plan-added', (plan) => {
            window.location.href = '/plans/' + plan.id;
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
                groups: isTeacher,
                plans: isTeacher,
                achievements: !isTeacher || isAdmin, // for testing purposes, show as admin
            };
        },
    },
    components: {
        InfoBox,
        ProgressBar,
        LogbookModal,
        KanbanModal,
        PlanModal,
        MediumModal,
    },
}
</script>