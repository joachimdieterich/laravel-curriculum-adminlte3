<template >
    <div
        v-if="(course.curriculum.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
        :id="'course-item-'+course.id"
        class="box box-objective nav-item-box-image pointer my-1"
        style="min-width: 200px !important;"
        :style="'border-bottom: 5px solid ' + course.curriculum.color">

        <a :href="'/courses/' + this.course.id"
           class="text-decoration-none text-black">

            <div v-if="course.curriculum.medium_id"
                 class="nav-item-box-image-size"
                 :style="{'background': 'url(/media/' + course.curriculum.medium_id + '?model=Curriculum&model_id=' + course.curriculum.id +') top center no-repeat', 'background-size': 'cover', }">
                <div class="nav-item-box-image-size"
                     style="width: 100% !important;"
                     :style="{backgroundColor: course.curriculum.color + ' !important',  'opacity': '0.5'}">
                </div>
            </div>
            <div v-else
                 class="nav-item-box-image-size text-center"
                 :style="{backgroundColor: course.curriculum.color + ' !important'}">
                <i class="fa fa-2x p-5 fa-columns nav-item-text text-white"></i>
            </div>
            <span class="bg-white text-center p-1 overflow-auto nav-item-box">
               <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                   {{ course.curriculum.title }}
               </h1>
               <p class="text-muted small"
                  v-html="htmlToText(course.curriculum.description)">
               </p>
            </span>

            <div class="btn btn-flat pull-right "
                 :id="'courseDropdown_' + course.curriculum.id"
                 style="position:absolute; top:0; right: 0; background-color: transparent;"
                 data-toggle="dropdown"
                 aria-expanded="false">
                <i class="fas fa-ellipsis-v"
                   :style="'color:' + $textcolor(course.curriculum.color)"></i>
                <div class="dropdown-menu dropdown-menu-right"
                     x-placement="left-start">
                    <button
                        :id="'delete-course-'+course.curriculum.id"
                        type="submit"
                        class="dropdown-item py-1 text-red"
                        @click.prevent="$parent.confirmItemDelete(course)">
                        <i class="fa fa-trash mr-2"></i>
                        {{ trans('global.expel') }}
                    </button>
                </div>
            </div>
        </a>
    </div>
</template>


<script>
    export default {
        props: {
            course: {},
            medium: {},
            format: '',
        },
        data() {
            return {
                subscriptions: {},
                errors: {},
                search: '',
            }
        },
        methods: {
            href: function (id) {
                return '/media/'+ id;
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
            },
        },
        mounted() {
            this.$eventHub.$on('filter', (filter) => {
                this.search = filter;
            })
        },

        beforeMount() {

        },



    }
</script>
