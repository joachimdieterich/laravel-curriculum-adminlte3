<template>
    <modal
        id="lms-modal"
        name="lms-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card"
             style="margin-bottom: 0 !important">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.lms.title_singular') }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable"
                            @click="token = false">
                        <i class="fa fa-user-lock"></i>
                    </button>
                    <button type="button" class="btn btn-tool draggable">
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">

                <token v-if="token === false"
                       v-on:newToken="onNewToken"></token>
                <span v-if="token !== false">
                    <div v-if="method === 'post' " class="form-group ">
                    <label for="courses">
                        {{ trans('global.course.title') }}
                    </label>
                    <select name="courses"
                            id="courses"
                            class="form-control select2 "
                            style="width:100%;">
                        <option></option>
                        <option v-for="entry in courses.courses" v-bind:value="entry.id">{{ entry.fullname }}</option>
                    </select>
                </div>

                    <div v-if="method === 'post'" class="form-group ">
                    <label for="course_contents">
                        Block
                    </label>
                    <select name="course_contents"
                            id="course_contents"
                            class="form-control select2 "
                            style="width:100%;">
                        <option></option>
                        <option v-for="item in course_contents" v-bind:value="item.id">{{ item.name }}</option>
                    </select>
                </div>

                    <div v-if="method === 'post'" class="form-group ">
                    <label for="course_items">
                        Aktivität/Modul
                    </label>
                    <select name="course_items"
                            id="course_items"
                            class="form-control select2 "
                            style="width:100%;">
                        <option></option>
                        <option v-for="item in course_content_items" v-bind:value="item.id">{{ item.name }}</option>
                    </select>
                </div>

<!--                <div v-if="method === 'post'" class="form-group ">
                    <label for="sharing_level">
                        Freigabe-Level
                    </label>
                    <select name="sharing_level"
                            id="sharing_level"
                            class="form-control select2 "
                            style="width:100%;">
                        <option></option>
                        <option v-for="item in sharing_levels" v-bind:value="item.id">{{ item.lang_de }}</option>
                    </select>
                </div>-->
                </span>

            </div>

            <div class="card-footer"
                 v-if="token !== false">
                <span class="pull-right">
                     <button class="btn btn-primary" @click="submit()">{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
        <div id="moodle"></div>
    </modal>
</template>

<script>
import Form from 'form-backend-validation';
import Token from './Token';

export default {
    data() {
        return {
            method: 'post',
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
            courses: {},
            course_contents: {},
            course_content_items: {},
            sharing_levels: {},

            requestUrl: '/lmsReferences',
        }
    },
    methods: {
        async loader() {
            axios.get('/lmsUserTokens')
                .then(response => {
                    this.token      = response.data.token;
                    this.lms_url    = response.data.lms_url;
                    if (this.lms_url !== ''){
                        this.getTokenFromLms(this.lms_url + 'mod/curriculum/get_token.php');
                    }
            }).catch(e => {
                error.log(response);
            });

            axios.get('/sharingLevels').then(response => {
                this.sharing_levels = response.data.sharingLevel;
            }).catch(e => {
                console.log(e);
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

        async loadCourses() {
            try {
                this.courses = (await axios.post(this.requestUrl + '/get', {
                    plugin: 'moodle',
                    ws_function: 'core_course_get_courses_by_field', //'core_course_get_courses',
                })).data.message;
                this.initSelect2();
                //console.log(this.courses);
            } catch (error) {
                console.log(error);
            }
        },
        async loadCourseContents(course_id) {
            this.form.course_id = parseInt(course_id)
            try {
                this.course_contents = (await axios.post(this.requestUrl + '/get', {
                    plugin: 'moodle',
                    ws_function: 'core_course_get_contents',
                    course_id: course_id
                })).data.message;
                if (typeof (this.course_contents.exception) !== "undefined") {
                    if (this.course_contents.exception == "moodle_exception") {
                        alert(this.course_contents.message);
                    }
                }
                this.initSelect2()
            } catch (error) {
                //this.errors = error.response.data.errors;
            }
        },
        async submit() {
            try {
                if (this.method === 'patch') {
                    this.location = (await axios.patch(this.requestUrl + '/' + this.form.id,)).data.message;
                } else {
                    this.location = (await axios.post(this.requestUrl, {
                        'plugin': 'moodle',
                        'course_id': this.form.course_id,
                        'course_content_id': this.form.course_content_id,
                        'course_item': this.form.course_item,
                        'referenceable_type': this.form.referenceable_type,
                        'referenceable_id': this.form.referenceable_id,
                        'sharing_level': this.form.sharing_level,
                    })).data.message;
                }
            } catch (error) {
                console.log(error);
            }
            this.$root.$emit('lmsUpdate')
            this.$modal.hide('lms-modal');
        },

        initSelect2() {
            $("#courses").select2({
                dropdownParent: $(".v--modal-overlay"),
                allowClear: false,
            }).on('select2:select', function (e) {
                this.loadCourseContents(e.params.data.id);
            }.bind(this))
                .val(this.form.course_id).trigger('change'); //set value

            $("#course_contents").select2({
                dropdownParent: $(".v--modal-overlay"),
                allowClear: false,
            }).on('select2:select', function (e) {
                this.form.course_content_id = parseInt(e.params.data.id)
                const index = this.course_contents.findIndex(            // Find the index of the status where we should replace the item
                    c => c.id === parseInt(e.params.data.id)
                );
                this.course_content_items = this.course_contents[index].modules
            }.bind(this))
                .val(this.form.course_content_id).trigger('change'); //set value

            $("#course_items").select2({
                dropdownParent: $(".v--modal-overlay"),
                allowClear: false,
            }).on('select2:select', function (e) {

                const index = this.course_content_items.findIndex(            // Find the index of the status where we should replace the item
                    i => i.id === parseInt(e.params.data.id)
                );
                this.form.course_item = this.course_content_items[index]
            }.bind(this))
                .val(this.form.course_content_id).trigger('change'); //set value

            /* $("#sharing_level").select2({
                 dropdownParent: $(".v--modal-overlay"),
                 allowClear: false,
             });*/
        },

        beforeOpen(event) {
            this.loader()
            this.courses = {};
            this.course_contents = {};

            if (event.params.referenceable_type) {
                this.form.referenceable_type = event.params.referenceable_type;
                this.form.referenceable_id = event.params.referenceable_id;
            }
            this.loadCourses();
        },
        beforeClose() {

        },
        opened() {
            this.courses = {};

        },

        close() {
            this.$modal.hide('lms-modal');
        },
    },
    components: {
        Token,
    }
}
</script>
