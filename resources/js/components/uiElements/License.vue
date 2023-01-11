<template>
    <span>
        <i v-if="licenseId === 1" class="far fa-question-circle"></i>
        <i v-if="licenseId === 2" class="far fa-copyright"></i>

        <i v-if="licenseId >= 3 " class="fab fa-creative-commons"></i>
        <i v-if="licenseId === 3" class="fab fa-creative-commons-zero"></i>
        <i v-if="licenseId > 3 && licenseId < 10 " class="fab fa-creative-commons-by"></i>
        <i v-if="licenseId === 5 || licenseId === 6" class="fab fa-creative-commons-nd"></i>
        <i v-if="licenseId === 6 || licenseId === 7 || licenseId === 8" class="fab fa-creative-commons-nc-eu"></i>
        <i v-if="licenseId === 8 || licenseId === 9" class="fab fa-creative-commons-sa"></i>

    </span>

</template>

<script>
    export default {
        props: {
            licenseId: '',
        },

    }
</script>
