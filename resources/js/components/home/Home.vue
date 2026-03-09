<template>
    <div
        id="home"
        class="row"
    >
        <InfoBox
            model="group"
            :text="trans('global.group.title')"
            icon="fa-users"
            icon-background-class="bg-purple"
        />

        <InfoBox
            model="logbook"
            :text="trans('global.logbook.title')"
            icon="fa-book"
            icon-background-class="bg-red"    
        />
    </div>
</template>
<script>
import InfoBox from '../uiElements/InfoBox.vue';

export default {
    name: 'Home',
    components: {
        InfoBox,
    },
}
</script>