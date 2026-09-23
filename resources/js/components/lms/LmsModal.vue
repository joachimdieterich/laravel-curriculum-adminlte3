<template>
    <Modal
        model="lms"
        modalName="lms-modal"
        url="/lmsReferences"
        title="global.lms.title_singular"
        :form="form"
        :allow-overflow="true"
        :disable-save-button="!form.course_id"
        :intercept-save="true"
        @opened="preProcessing()"
        @save="submit()"
    >
        <template #general>
            <Token v-if="!token && !loading"
                @newToken="onNewToken"
            />
            <div v-else>
                <Select2 v-if="courses.length"
                    id="course_select"
                    :list="courses"
                    css="mb-3"
                    model="course"
                    option_id="id"
                    option_label="fullname"
                    :selected="null"
                    @selectedValue="id => {
                        form.course_content_id = null;
                        loadCourseContents(parseInt(id[0]));
                    }"
                />

                <Select2 v-if="form.course_id && course_contents.length"
                    id="course_contents"
                    :list="course_contents"
                    css="mb-3"
                    :label="trans('global.course.content')"
                    model="course"
                    option_id="id"
                    option_label="name"
                    :selected="null"
                    @selectedValue="id => {
                        form.course_content_id = parseInt(id[0]);
                        loadCourseItems(parseInt(id[0]));
                    }"
                />

                <Select2 v-if="form.course_content_id && course_content_items.length"
                    id="course_items"
                    :list="course_content_items"
                    :label="trans('global.course.content_item')"
                    model="course"
                    option_id="id"
                    option_label="name"
                    :selected="null"
                    @selectedValue="id => setItems(parseInt(id[0]))"
                />

                <div v-if="loading"
                    class="overlay flex-column"
                >
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                    <span>{{ trans('global.loading') }}</span>
                </div>
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import Token from "./Token.vue";

export default {
    name: 'lms-modal',
    components: {
        Modal,
        Token,
        Select2,
    },
    props: {
        params: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                plugin: 'moodle',
                referenceable_type: null,
                referenceable_id: null,
                course_id: null,
                course_content_id: null,
                course_item: null,
                sharing_level: 2,
            }),
            token: true, // don't actually store the token itself, only if it can be fetched
            lms_url: '',
            courses: [],
            course_contents: [],
            course_content_items: [],
            sharing_levels: [],
            loading: false,
        }
    },
    methods: {
        preProcessing() {
            if (this.courses.length === 0) {
                this.loader();
                this.loadCourses();
            }
        },
        loader() {
            this.course_contents = [];
            this.course_content_items = [];
            this.course_content_id = null;
            this.course_item = null;

            axios.get('/lmsUserTokens')
                .then(response => {
                    this.token      = response.data.token;
                    this.lms_url    = response.data.lms_url;
                }).catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
        loadCourses() {
            this.loading = true;
            this.course_content_items = [];
            this.course_item = null;

            axios.post('/lmsReferences/get', {
                plugin: 'moodle',
                ws_function: 'core_course_get_courses_by_field',
            })
                .then(r => {
                    this.courses = r.data.entries;
                    this.loading = false;
                }).catch(e => {
                    console.log(e);
                    this.loading = false;
                    this.toast.error(this.errorMessage(e));
                });
        },
        loadCourseContents(course_id) {
            this.loading = true;
            this.form.course_id = course_id;
            // save the course name incase no further items are selected
            this.form.course_item = {
                name: this.courses.find(c => c.id === course_id).fullname,
                url: this.lms_url + 'course/view.php?id=' + course_id
            };

            axios.post('/lmsReferences/get', {
                plugin: 'moodle',
                ws_function: 'core_course_get_contents',
                course_id: course_id,
            })
                .then(r => {
                    this.course_contents = r.data.entries;
                    if (typeof (this.course_contents.exception) !== "undefined") {
                        if (this.course_contents.exception == "moodle_exception") {
                            alert(this.course_contents.message);
                        }
                    }
                    this.loading = false;
                }).catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
        loadCourseItems(id) {
            const index = this.course_contents.findIndex(c => c.id === id);
            this.form.course_item = {
                name: this.course_contents[index].name,
                url: this.lms_url + 'course/view.php?id=' + this.form.course_id + '&section=' + index,
            };
            this.course_content_items = this.course_contents[index].modules;
        },
        setItems(id) {
            const index = this.course_content_items.findIndex(i => i.id === id);
            // manually select needed attributes, to avoid saving irrelevant data
            // since this field will be saved as JSON, the attributes cannot be distinctively selected in the backend
            this.form.course_item = {
                modicon: this.course_content_items[index].modicon,
                name: this.course_content_items[index].name,
                url: this.course_content_items[index].url,
            };
        },
        onNewToken() {
            this.token = true;
            this.loadCourses();
        },
    },
}
</script>