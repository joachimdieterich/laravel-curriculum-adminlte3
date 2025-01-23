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
                    <button type="button" class="btn btn-tool draggable"
                            @click="token = false">
                        <i class="fa fa-user-lock"></i>
                    </button>
                    <button type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <token v-if="token === false"
                       v-on:newToken="onNewToken"></token>
                <span v-if="token !== false">
                    <div v-if="this.courses.length"
                         class="form-group ">
                         <Select2
                             id="course_select"
                             name="course_select"
                             :list="courses"
                             model="course"
                             option_id="id"
                             option_label="fullname"
                             :selected="null"
                             @selectedValue="(id) => {
                                  this.loadCourseContents(id);
                                }"
                             >
                        </Select2>
                    </div>

                    <div v-if="this.course_contents.length"
                         class="form-group ">
                         <Select2
                             id="course_contents"
                             name="course_contents"
                             :list="course_contents"
                             model="course"
                             option_id="id"
                             option_label="name"
                             :selected="null"
                             @selectedValue="(id) => {
                                  this.loadCourseItems(id);
                                }"
                         >
                        </Select2>
                    </div>

                    <div v-if="this.form.course_content_id"
                         class="form-group ">
                         <Select2
                             id="course_items"
                             name="course_items"
                             :list="course_content_items"
                             model="course"
                             option_id="id"
                             option_label="name"
                             :selected="null"
                             @selectedValue="(id) => {
                                 this.setItems(id);
                                }"
                         >
                        </Select2>

                    </div>

<!--                    <div v-if="this.form.course_content_id"
                         class="form-group ">
                         <Select2
                             id="sharing_level"
                             name="sharing_level"
                             :list="sharing_levels"
                             model="sharinglevel"
                             option_id="id"
                             option_label="lang_de"
                             label="Freigabelevel"
                             :selected="this.form.sharing_level"
                             @selectedValue="(id) => {
                                 this.form.sharing_level = id;
                                }"
                         >
                        </Select2>
                    </div>-->

                    <div v-if="loading == true "
                         class="overlay text-center"
                         style="width:100% !important;">
                        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                        <span class="sr-only">Loading...</span>
                    </div>
                </span>
            </div>

                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="grade-cancel"
                             type="button"
                             class="btn btn-default"
                             @click="globalStore?.closeModal($options.name)">
                             {{ trans('global.cancel') }}
                         </button>
                         <button
                             id="grade-save"
                             class="btn btn-primary"
                             @click="submit()" >
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
    components:{
        Token,
        Select2,
    },
    props: {
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
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
            url: '/lmsReferences',
            form: new Form({
                'id': null,
                'referenceable_type': null,
                'referenceable_id': null,
                'course_id': null,
                'course_content_id': null,
                'course_item': null,
                'sharing_level': 2,
            }),
            token: false,
            lms_url: '',
            courses: [],
            course_contents: [],
            course_content_items: [],
            sharing_levels: [],
            loading: false
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
            axios.post(this.url + '/get', {
                    plugin: 'moodle',
                    ws_function: 'core_course_get_courses_by_field',
                })
                .then(r => {
                    //console.log(r.data);
                    this.courses = r.data
                    this.loading = false;
                }).catch(e => {
                    console.log(e);
                });
        },
        loadCourseContents(course_id) {
            this.loading = true;
            this.form.course_id = parseInt(course_id)
            axios.post(this.url + '/get', {
                plugin: 'moodle',
                ws_function: 'core_course_get_contents',
                course_id: course_id
            })
                .then(r => {
                    //console.log(r.data);
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
            this.form.course_content_id = parseInt(id)
            const index = this.course_contents.findIndex(
                c => c.id === parseInt(id)
            );
            this.course_content_items = this.course_contents[index].modules
        },
        setItems(id) {
            const index = this.course_content_items.findIndex(            // Find the index of the status where we should replace the item
                i => i.id === parseInt(id)
            );
            this.form.course_item = this.course_content_items[index]
        },
        submit(method) {
            if (this.form.course_item != null) {
                if (method == 'patch') {
                    this.update();
                } else {
                    this.add();
                }
            } else {
                this.errorNotification('Bitte alle Felder auswählen.');
            }
        },
        add() {
            axios.post(this.url, {
                'plugin': 'moodle',
                'course_id': this.form.course_id,
                'course_content_id': this.form.course_content_id,
                'course_item': this.form.course_item,
                'referenceable_type': this.form.referenceable_type,
                'referenceable_id': this.form.referenceable_id,
                'sharing_level': this.form.sharing_level,
            })
                .then(r => {
                    this.$eventHub.emit('lms-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
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
        onNewToken(token) {
            this.token = token;
            this.loadCourses();
        },
        errorNotification(message) {
            this.toast.error(message, {
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false
            });
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
                    if (this.form.id != ''){
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
