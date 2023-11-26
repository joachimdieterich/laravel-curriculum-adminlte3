<template>
    <div>
        <div
            v-if="visible"
            :id="'course-add'"
            class="box box-objective nav-item-box-image pointer my-1"
            style="min-width: 200px !important; border-bottom: 5px solid #28a745"
            @click="open()">
            <div class="nav-item-box-image-size text-center bg-success">
                <i class="fa fa-2x p-5 fa-plus nav-item-text text-white"></i>
            </div>

            <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ trans('global.course.create') }}
                   </h1>
            </span>
        </div>
        <!-- Create Modal -->
        <CourseAdd
            id="modal-course-form"
            :method="method"
            :group="group"
        />
    </div>

</template>
<script>
import CourseAdd from "./CourseAdd";

export default {
    name: 'CourseIndexAddWidget',
    props: {
        group: {},
        visible: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            method: {
                type: String,
                default: 'post'
            }
        }
    },
    methods: {
       open(method = 'post'){
           this.method = method;
           $('#modal-course-form').modal('show');
           this.$eventHub.$emit('load_curricula');
       },
    },
    mounted() {},
    components: {
        CourseAdd,
    },
}
</script>
