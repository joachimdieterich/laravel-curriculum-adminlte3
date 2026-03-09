<template>
    <div
        id="home"
        class="row"
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
                @error="handleError"
            />

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
                @error="handleError"
            />
    
            <InfoBox
                model="plans"
                :text="trans('global.plan.title')"
                icon="fa-clipboard-list"
                icon-background-class="bg-green"
                @error="handleError"
            />
        </div>
    </div>
</template>
<script>
import InfoBox from '../uiElements/InfoBox.vue';
import { useToast } from 'vue-toastification';

export default {
    name: 'Home',
    props: {
        role: {
            type: String,
            required: true,
        },
    },
    setup() {
        const toast = useToast();
        return {
            toast,
        };
    },
    methods: {
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
    },
}
</script>