<template xmlns="http://www.w3.org/1999/html">
    <div class="card h-100 mb-2 bg-gradient-dark"
         v-if="meeting.livestream">
        <div
            v-html="meeting.livestream">
        </div>
    </div>
</template>
<script>
export default {
    name: 'Livestream',
    props: {
        'meeting': Object,
    },
}
</script>
