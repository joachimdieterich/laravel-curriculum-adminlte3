<template>
    <div class="row">
        <table id="course-datatable" style="display: none;"></table>
        <div id="course-content">
            <div class="py-2">
                <CourseIndexWidget
                    v-for="(course,index) in courses"
                    :key="index+'_course_'+course.id"
                    :course="course"
                    :search="search.toLowerCase()"/>
                <CourseIndexAddWidget
                    :group="group"
                    v-if="checkPermission('group_enrolment')"/>
            </div>
            <Modal
                :id="'courseModal'"
                css="danger"
                :title="trans('global.expel')"
                :text="trans('global.expel_helper')"
                :ok_label="trans('global.expel')"
                v-on:ok="destroy()"
            />
        </div>
    </div>
</template>

<script>
import CourseIndexWidget from "./CourseIndexWidget";
import CourseIndexAddWidget from "./CourseIndexAddWidget";

const Modal = () => import('./../uiElements/Modal');

export default {
    props: {
        group: Object,
    },
    data() {
        return {
            courses: [],
            subscriptions: {},
            search: '',
            url: '/courses',
            errors: {},
            tempId: Number,
            currentCourse: {}
        }
    },
    methods: {
        loaderEvent(){
            if ($.fn.dataTable.isDataTable( '#course-datatable' )){
                $('#course-datatable').DataTable().ajax.url(this.url + '?group_id=' + this.group.id).load();
            } else {
                const dtObject = $('#course-datatable').DataTable({
                    ajax: this.url + '?group_id=' + this.group.id,
                    dom: 'tilpr',
                    pageLength: 50,
                    language: {
                        url: '/datatables/i18n/German.json',
                        paginate: {
                            "first":      '<i class="fa fa-angle-double-left"></id>',
                            "last":       '<i class="fa fa-angle-double-right"></id>',
                            "next":       '<i class="fa fa-angle-right"></id>',
                            "previous":   '<i class="fa fa-angle-left"></id>',
                        },
                    },
                    columns: [ // only gets attributes used in this component
                        { title: 'id', 'data': "curriculum.id", searchable: false },
                        { title: 'title', 'data': "curriculum.title", searchable: true },
                        { title: 'description', 'data': "curriculum.description", searchable: true },
                    ],
                }).on('draw.dt', () => { // checks if the datatable-data changes, to update the videoconference-data
                    this.courses = dtObject.rows({ page: 'current' }).data().toArray();
                    $('#course-content').insertBefore('#course-datatable');
                });
            }
        },
        confirmItemDelete(course){
            $('#courseModal').modal('show');
            this.currentCourse = course;
        },
        destroy() {
            axios.delete(
                '/curricula/expel', {
                    data :{
                        'expel_list' : {
                            0: {
                                'group_id' : this.group.id,
                                'curriculum_id': {
                                    0 : this.currentCourse.curriculum.id
                                }
                            }
                        }
                    }
                }
            )
                .then(res => {
                    this.$eventHub.$emit("course-updated", res.data.message);
                })
                .catch(error => { // Handle the error returned from our request
                    console.log(error)
                });
        },

    },
    mounted() {
        this.loaderEvent();

        this.$eventHub.$on('filter', (filter) => {
            $('#course-datatable').DataTable().search(filter).draw();
        });
        this.$eventHub.$emit('showSearchbar');

        const parent = this;
        // checks if the datatable-data changes, to update the course-data
        $('#course-datatable').on('draw.dt', () => {
            parent.courses = $('#course-datatable').DataTable().rows({ page: 'current' }).data().toArray();
        });

        // place the content where the table would normally be
        setTimeout(() => {
            $('#course-content').insertBefore('#course-datatable');
        }, 250); // needs delay, because the wrapper only appears after receiving first ajax-response
    },
    components: {
        CourseIndexWidget,
        CourseIndexAddWidget,
        Modal
    },
}
</script>
<style>
#course-datatable_wrapper { width: 100%; }
@media only screen and (min-width: 992px) {
    #course-datatable_wrapper { padding: 0px 15px; }
}
</style>
<style scoped>
.nav-link:hover {
    cursor: default;
    user-select: none;
}

.nav-item:hover .nav-link:not(.active) {
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer;
}
</style>
