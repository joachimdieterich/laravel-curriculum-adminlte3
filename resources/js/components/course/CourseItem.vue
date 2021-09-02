<template >
<div>

    <div
        :id="'navigator-item-'+course.id"
        class="box box-objective pointer  my-1"
        style="height: 300px !important;
        min-width: 200px !important;
        background:top center no-repeat; background-size: cover;
        padding: 0;"
        @click="openCurriculum()">
        <div style="height: 150px !important;
        min-width: 198px !important;
        padding: 0;
        background-size: cover;"
        v-bind:style="{ backgroundImage: 'url(\'/media/' + course.curriculum.medium_id + ') ' }">
            <a v-can="'course_show'"
               @click.stop.prevent="openCourse()"
               class="btn btn-primary btn-sm pull-right  m-2">
                <small><i class="fa fa-user-graduate"></i></small>
            </a>
        </div>

        <span
            class="bg-white text-center p-1 overflow-auto "
            style="position:absolute; bottom:0px; height: 150px; width:100%;">
            <h6 class="events-heading pt-1 hyphens" v-html="course.curriculum.title"></h6>
            <!--<p class=" text-muted small" v-html="course.curriculum.description"></p>-->
       </span>
    </div>

</div>

</template>


<script>
    export default {
        props: {
            course: {},
            medium: {},
            format: ''
        },
        data() {
            return {
                subscriptions: {},
                errors: {}
            }
        },
        methods: {

            href: function (id) {
                return '/media/'+ id;
            },
            openCurriculum(){
                window.location.href = '/curricula/'+this.course.curriculum.id;
            },
            openCourse(){
                window.location.href = '/courses/'+this.course.id;
            },
            iconCss(mimeType) {
                switch (true) {
                    case mimeType.startsWith("image"):
                        return "fa fa-file-image";
                        break;
                    case mimeType.startsWith("video"):
                        return "fa fa-file-video";
                        break;
                    case mimeType.startsWith("application/pdf"):
                        return "fa fa-file-pdf";
                        break;
                    default:
                        return "fa fa-file";
                        break;
                }
            }
        },

        beforeMount() {

        },



    }
</script>
