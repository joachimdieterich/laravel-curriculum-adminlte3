<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ trans('global.lms.title_singular') }}
                    </h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool draggable"
                            @click="token = false"
                        >
                            <i class="fa fa-user-lock"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div
                    class="modal-body"
                    style="overflow-y: visible;"
                >
                    <div class="card">
                        <div class="card-body">
                            <Token v-if="!token"
                                @newToken="onNewToken"
                            />
                            <span v-if="token">
                                <div v-if="courses.length"
                                    class="form-group"
                                >
                                    <Select2
                                        id="course_select"
                                        name="course_select"
                                        :list="courses"
                                        model="course"
                                        option_id="id"
                                        option_label="fullname"
                                        :selected="null"
                                        @selectedValue="(id) => {
                                            this.form.course_content_id = null;
                                            this.loadCourseContents(parseInt(id[0]));
                                        }"
                                    />
                                </div>
            
                                <div v-if="form.course_id && course_contents.length"
                                    class="form-group"
                                >
                                    <Select2
                                        id="course_contents"
                                        name="course_contents"
                                        :list="course_contents"
                                        model="course"
                                        option_id="id"
                                        option_label="name"
                                        :selected="null"
                                        @selectedValue="(id) => {
                                            this.form.course_content_id = parseInt(id[0]);
                                            this.form.course_item = null;
                                            this.loadCourseItems(parseInt(id[0]));
                                        }"
                                    />
                                </div>
            
                                <div v-if="form.course_content_id"
                                    class="form-group"
                                >
                                    <Select2
                                        id="course_items"
                                        name="course_items"
                                        :list="course_content_items"
                                        model="course"
                                        option_id="id"
                                        option_label="name"
                                        :selected="null"
                                        @selectedValue="(id) => {
                                            this.setItems(parseInt(id[0]));
                                        }"
                                    />
                                </div>
            
                                <div v-if="loading"
                                    class="overlay text-center"
                                    style="width:100% !important;"
                                >
                                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="grade-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="grade-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.course_id"
                            @click="submit()"
                        >
                            {{ trans('global.save') }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";
import Token from "./Token.vue";
import {useToast} from "vue-toastification";

export default {
    name: 'lms-modal',
    components: {
        Token,
        Select2,
    },
    props: {
        params: {
            type: Object,
            default: null,
        },
    },
    setup() {
        const toast = useToast();
        const globalStore = useGlobalStore();
        return {
            globalStore,
            toast
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            form: new Form({
                id: null,
                referenceable_type: null,
                referenceable_id: null,
                course_id: null,
                course_content_id: null,
                course_item: null,
                sharing_level: 2,
            }),
            token: false, // don't actually store the token itself, only if it can be fetched
            lms_url: '',
            courses: [],
            course_contents: [],
            course_content_items: [],
            sharing_levels: [],
            loading: false,
        }
    },
    methods: {
        loader() {
            this.course_contents = [];
            this.course_content_items = [];
            this.course_content_id = null;
            this.course_item = null;
            axios.get('/lmsUserTokens')
                .then(response => {
                    this.token      = response.data.token;
                    this.lms_url    = response.data.lms_url;
                    if (this.lms_url !== ''){
                        this.getTokenFromLms(this.lms_url + '/mod/curriculum/get_token.php');
                    }
                }).catch(e => {
                    console.log(e);
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
                    this.courses = r.data
                    this.loading = false;
                }).catch(e => {
                    console.log(e);
                });
        },
        loadCourseContents(course_id) {
            this.loading = true;
            this.form.course_id = course_id;

            axios.post('/lmsReferences/get', {
                plugin: 'moodle',
                ws_function: 'core_course_get_contents',
                course_id: course_id,
            })
                .then(r => {
                    this.course_contents = r.data
                    if (typeof (this.course_contents.exception) !== "undefined") {
                        if (this.course_contents.exception == "moodle_exception") {
                            alert(this.course_contents.message);
                        }
                    }
                    this.loading = false;
                }).catch(e => {
                    console.log(e);
                });
        },
        loadCourseItems(id) {
            const index = this.course_contents.findIndex(c => c.id === id);
            this.course_content_items = this.course_contents[index].modules;
        },
        setItems(id) {
            const index = this.course_content_items.findIndex(i => i.id === id);
            this.form.course_item = this.course_content_items[index];
        },
        submit(method) {
            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post('/lmsReferences', {
                plugin: 'moodle',
                course_id: this.form.course_id,
                course_content_id: this.form.course_content_id,
                course_item: this.form.course_item,
                referenceable_type: this.form.referenceable_type,
                referenceable_id: this.form.referenceable_id,
                sharing_level: this.form.sharing_level,
            })
                .then(r => {
                    this.$eventHub.emit('lms-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch('/lmsReferences/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('lms-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        async getTokenFromLms(url) {
            axios.get(url)
                .then(response => {
                    this.token = response.data.privatetoken ;
                    this.setUserToken();
                })
                .catch(e => {
                    console.log(e);
                });
        },
        async setUserToken() {
            try {
                const token = (await axios.post('/lmsUserTokens',
                    {
                        'token': this.form.token,
                    })).data.token;
                this.$emit('newToken', token)

            } catch (error) {
                console.log(error);
            }
        },
        onNewToken() {
            this.token = true;
            this.loadCourses();
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });

        this.loader();
        this.loadCourses();
    },
}
</script>