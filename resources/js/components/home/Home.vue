<template>
    <div
        id="home"
        class="d-flex flex-column flex-md-row mx-lg-3"
    >
        <div
            id="home-left"
            class="col-12 col-md-6"
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
        </div>

        <div
            id="home-right"
            class="col-12 col-md-6"
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
        <PlanModal/>
    </div>
</template>
<script>
import InfoBox from '../uiElements/InfoBox.vue';
import ProgressBar from '../uiElements/ProgressBar.vue';
import LogbookModal from '../logbook/LogbookModal.vue';
import PlanModal from '../plan/PlanModal.vue';
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
    },
    computed: {
        isVisible() {
            return {
                groups: this.checkPermission('is_teacher'),
                plans: this.checkPermission('is_teacher'),
            };
        },
    },
    components: {
        InfoBox,
        ProgressBar,
        LogbookModal,
        PlanModal,
    },
}
</script>