<template>
    <Modal
        model="lms"
        modalName="lms-modal"
        url="/lmsReferences"
        title="global.lms.title_singular"
        :form="form"
        :allow-overflow="true"
        :disable-save-button="!form.course_id"
        :show-save-button="token || loading"
        @opened="preProcessing()"
    >
        <template #general>
            <Token v-if="!token && !loading"
                @newToken="onNewToken"
            />
            <div v-else>
                <Select2 v-if="organizations.length > 1"
                    id="organization-select"
                    :list="organizations"
                    css="mb-3"
                    model="organization"
                    :selected="null"
                    @selectedValue="id => {
                        organization_id = id[0];
                        lms_url = organizations.find(o => o.id == id[0]).lms_url;
                        loadCourses();
                    }"
                
                />
                <Select2 v-if="courses.length"
                    id="course-select"
                    :list="courses"
                    model="course"
                    option_label="fullname"
                    :selected="null"
                    @selectedValue="id => {
                        form.course_content_id = null;
                        loadCourseContents(parseInt(id[0]));
                    }"
                />

                <Select2 v-if="form.course_id && course_contents.length"
                    id="course-contents"
                    :list="course_contents"
                    css="mt-3"
                    :label="trans('global.course.content')"
                    model="course"
                    option_label="name"
                    :selected="null"
                    @selectedValue="id => {
                        form.course_content_id = parseInt(id[0]);
                        loadCourseItems(parseInt(id[0]));
                    }"
                />

                <Select2 v-if="form.course_content_id && course_content_items.length"
                    id="course-items"
                    :list="course_content_items"
                    css="mt-3"
                    :label="trans('global.course.content_item')"
                    model="course"
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

        <template #footer-left>
            <button v-if="organizations.length !== 0"
                type="button"
                class="btn btn-icon me-auto"
                :title="trans('global.lms.token')"
                @click="token = !token"
            >
                <i class="fa fa-user-lock"></i>
            </button>
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
            organizations: [],
            organization_id: null,
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
            }
        },
        loader() {
            this.course_contents      = [];
            this.course_content_items = [];
            this.course_content_id    = null;
            this.course_item          = null;

            axios.get('/lmsUserTokens')
                .then(response => {
                    this.organizations = response.data;
                    // if more than one token is found, the user must select the organization first
                    if (this.organizations.length === 1) {
                        this.organization_id = this.organizations[0].id;
                        this.lms_url         = this.organizations[0].lms_url;
                        this.loadCourses();
                    } else if (this.organizations.length === 0) {
                        this.token = false;
                    }
                }).catch(e => {
                    console.log(e);
                    this.toast.error(this.errorMessage(e));
                });
        },
        loadCourses() {
            this.loading              = true;
            this.course_content_items = [];
            this.course_item          = null;

            axios.post('/lmsReferences/get', {
                plugin: 'moodle',
                organization_id: this.organization_id,
                ws_function: 'core_course_get_courses_by_field',
            })
                .then(r => {
                    this.courses = r.data.entries;
                    this.loading = false;
                }).catch(e => {
                    console.log(e);
                    this.loading = false;
                    this.toast.error(this.errorMessage(e));
                    // if the token is invalid, it will be deleted in the backend and a 400 error will be returned
                    if (e.response.status === 400) {
                        this.token = false;
                    }
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
        onNewToken(token) {
            this.token = true;
            this.organization_id = token.organization_id;
            this.loadCourses();
        },
    },
}
</script>