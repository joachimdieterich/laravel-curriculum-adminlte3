<template>
    <div>
        <ul class="nav nav-tabs ">
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setLocalStorage('#note_view', '#note_view_all')"
            >
                <a class="nav-link show link-muted"
                   :class="checkLocalStorage('#note_view', '#note_view_all', 'active', true)"
                   href="#all"
                   @click="loadNotes('all')"
                   data-toggle="tab">
                    <i class="fa fa-sticky-note pr-1"></i>
                    <span v-if="help">{{ trans('global.note.title') }}</span>
                </a>
            </li>
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setLocalStorage('#note_view', '#note_view_users')"
            >
                <a class="nav-link show link-muted"
                   :class="checkLocalStorage('#note_view', '#note_view_users')"
                   href="#users"
                   @click="loadNotes('User')"
                   data-toggle="tab">
                    <i class="fa fa-user pr-1"></i>
                    <span v-if="help">{{ trans('global.user.title') }}</span>
                </a>
            </li>
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setLocalStorage('#note_view', '#note_view_groups')"
            >
                <a class="nav-link show link-muted"
                   :class="checkLocalStorage('#note_view', '#note_view_groups')"
                   href="#users"
                   @click="loadNotes('Group')"
                   data-toggle="tab">
                    <i class="fa fa-users pr-1"></i>
                    <span v-if="help">{{ trans('global.group.title') }}</span>
                </a>
            </li>
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setLocalStorage('#note_view', '#note_view_achievements')"
            >
                <a class="nav-link show link-muted"
                   :class="checkLocalStorage('#note_view', '#note_view_achievements')"
                   href="#achievements"
                   @click="loadNotes('Achievement')"
                   data-toggle="tab">
                    <i class="fa fa-bullseye pr-1"></i>
                    <span v-if="help">{{ trans('global.achievement.title') }}</span>
                </a>
            </li>
            <li v-permission="'note_create'"
                class="nav-item ml-auto small pull-right"
            >
                <a v-if="edit === false"
                    class="nav-link show link-muted pointer"
                    @click="toggleEdit()"
                >
                    <i class="fa fa-plus pr-1"></i>
                </a>
            </li>
        </ul>
        <div class="col-md-12 p-0">
            <div id="logbooks_filter" class="dataTables_filter mb-0 rounded-0">
                <label>
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search"
                    >
                </label>
            </div>
        </div>
        <div class="tab-content card-footer card-comments">
            <div class="tab-pane show active">
                <div :class="[edit ? 'note_editor_selector': 'hide']">
                    <div v-if="notables.length > 0"
                         class="form-group "
                         :class="[(method == 'patch') ? 'hide': '']">
                        <label for="title">{{ trans('global.'+ form.notable_type) }}</label>
                        <select
                            name="notable"
                            id="notable"
                            class="form-control select2"
                            style="width:100%;">
                            <option
                                v-for="(item,index) in notables"
                                :value="item.id">
                                <span v-if="item.firstname">{{ item.firstname }} {{ item.lastname }}</span>
                                <span v-else>{{ item.title }}</span>
                            </option>
                        </select>
                    </div>
                    <div class="form-group ">
                        <label for="title">{{ trans('global.task.fields.title') }}</label>
                        <input
                            type="text" id="title"
                            name="title"
                            class="form-control"
                            v-model="form.title"
                            :placeholder="trans('global.task.fields.title')"
                        />
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>
                    <div class="form-group ">
                        <label for="note_content">{{ trans('global.note_text') }}</label>
                        <textarea
                            id="note_content"
                            name="note_content"
                            class="form-control content my-editor "
                            v-model="form.content"
                        ></textarea>
                        <p class="help-block" v-if="form.errors.content" v-text="form.errors.content[0]"></p>
                    </div>
                    <button
                        class="btn btn-primary pull-right"
                        @click="submit">
                        {{ trans('global.save') }}
                    </button>
                    <button
                        class="btn pull-right"
                        @click="toggleEdit">
                        {{ trans('global.cancel') }}
                    </button>

                    <div class="clearfix border-bottom pb-2"></div>
                </div>

                <div v-for="(item,index) in notes"
                     v-if="showNote(item)"
                     class="card-comment"
                >
                    <div class="comment-text ml-0"
                         @mouseover="hover = item.id"
                         @mouseleave="hover = false"
                    >
                        <span :class="'note_editor_hide_placeholder_'+item.id">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex flex-wrap">
                                    <div class="d-flex align-items-center">
                                        <span v-if="item.notable"
                                            class="mr-1"
                                        >
                                            <a v-if="item.notable_type === 'App\\User'"
                                                :href="'/users/'+item.notable.id"
                                                class="text-bold text-decoration-none text-gray-dark">{{item.notable.firstname }} {{item.notable.lastname }} </a>
                                            <a v-else-if="item.notable_type === 'App\\Group'"
                                                :href="'/groups/' + item.notable.id"
                                                class="text-bold text-gray-dark">{{ item.notable.title }}</a>
                                            <a v-else-if="item.notable_type === 'App\\Achievement'"
                                                :href="'/enablingObjectives/' + item.notable.referenceable_id"
                                                class="text-bold text-gray-dark"><i class="fa fa-link" ></i></a>
                                            <a v-else
                                                class="text-bold text-gray-dark">{{item.notable.title }} </a>
                                        </span>
                                        <span class="text-gray-dark">{{ item.title }}</span>
                                        <small class="ml-2 badge badge-info user-select-none">{{ trans('global.'+item.notable_type) }}</small>
                                        <span v-if="hover == item.id"
                                            class="d-flex user-select-none"
                                        >
                                            <i class="text-muted fa fa-pencil-alt pointer p-1 ml-1"
                                                style="font-weight: 900;"
                                                @click="editNote(index)"
                                            ></i>
                                            <i class="text-muted fa fa-eye pointer p-1 ml-1"
                                                style="font-weight: 900;"
                                                @click="viewNote(index)"
                                            ></i>
                                        </span>
                                    </div>
                                    <!-- force new row -->
                                    <div style="flex-basis: 100%"></div>
                                    <!-- name-badge -->
                                    <div v-if="users !== undefined">
                                        <small class="badge badge-secondary">{{ users[item.notable.user_id] }}</small>
                                    </div>
                                </div>
                                <div class="d-inline-flex align-items-center justify-content-end" style="height: fit-content;">
                                    <small class="text-muted pull-right">
                                        <i v-if="hover == item.id"
                                            class="text-danger fa fa-trash pointer"
                                            style="padding: 3px;"
                                            @click="destroy(item.id, index)"
                                        ></i>
                                        <a class="pointer link-muted text-decoration-none pl-1"
                                            @click="toggleTimestampFormatDiffForHumans()"
                                        >
                                            <span v-if="item.created_at == item.updated_at">
                                                <i class="fa fa-plus-circle"></i> {{ formatTime(item.created_at) }}
                                            </span>
                                            <span v-if="item.created_at != item.updated_at">
                                                <i class="fa fa-plus-circle"></i> {{ formatTime(item.created_at) }}
                                                <i class="fas fa-pencil-alt"></i> {{ formatTime(item.updated_at) }}
                                            </span>
                                        </a>
                                    </small>
                                </div>
                            </div>
                            <span class="note-content" v-dompurify-html="item.content"></span>
                        </span>
                        <span :class="'note_editor_placeholder_'+item.id"></span>
                    </div>
                </div>
            </div>
        </div>
        <NoteShowModal></NoteShowModal>
    </div>
</template>

<script>
import Form from 'form-backend-validation';
import NoteShowModal from './NoteShowModal.vue';

// const moment =
//     () => import('moment');
import moment from 'moment';

export default {
    props: {
        notable_type: { type: String },
        notable_id: { type: [Number, Array] }, // either single id or array of ids
        show_tabs: { type: Boolean, default: true },
        users: { type: Object, default: undefined },
    },
    data() {
        return {
            component_id: this._uid,
            method: 'post',
            requestUrl: '/notes',
            notes: {},
            form: new Form({
                'id': '',
                'title': '',
                'content': '',
                'notable_type': '' ,
                'notable_id': '',
            }),
            edit: false,
            hover: '',
            help: false,
            search: '',
            timeFormatDiffForHumans: true,
            notables: {},
        }
    },
    methods: {
        async load() {
            const params = '?json=true'+ (this.form.notable_type ? '&notable_type=' + this.form.notable_type : '') + (this.form.notable_id ? '&notable_id=' + this.form.notable_id : '') ;

            try {
                this.notes = (await axios.get('/notes'+params)).data;
            } catch(error) {
                //console.log('loading failed')
            }
        },
        async loadNotables() {
            this.notables = {};
            const params = '?json=true';
            let url = '';
            switch (this.form.notable_type) {
                case "App\\User":
                    url = "users";
                    break;
                case "App\\Group":
                    url = "groups";
                    break;
                default:
                    return;
            }

            try {
                this.notables = JSON.parse((await axios.get('/' + url + params)).data[url])
                $('#notable').select2("destroy");
                this.$nextTick(function () {
                    this.syncSelect2();
                })
            } catch(error) {
                //console.log('loading failed')
            }
        },
        syncSelect2() {
            $("#notable").select2({
                dropdownParent: $("#notable").parent(),
                allowClear: false
            }).on('select2:select', function (e) {
                this.form.notable_id = e.params.data.id;
            }.bind(this));
        },
        async submit() {
            let method = this.method.toLowerCase();
            this.form.content = tinyMCE.get('note_content').getContent();
            // .setContent('') will crash tinymce, when closing and reopening the modal
            tinymce.get('note_content').execCommand('mceSetContent', false, '');

            let currentPath = '';
            if (method === 'patch') {
                currentPath =  '/' + this.form.id;
                this.method = 'post';
                // remove note, it will be added after submit again
                const index = this.notes.findIndex(note => note.id === this.form.id);
                this.notes.splice(index, 1);
            }

            this.toggleEdit();
            // wait for request to be handled, because this.form will have empty values after
            await this.form.submit(method, this.requestUrl + currentPath)
                .then(response => this.notes.unshift(response))
                .catch(response => console.log(response));

            this.prefillForm();
        },
        toggleEdit() {
            this.edit = !this.edit;
            if (this.edit === true) {
                this.loadNotables();
            } else {
                // if edit got cancelled, show element again
                this.$el.querySelector('span.hide')?.classList.remove('hide');
            }
        },
        editNote(index) {
            this.edit = true;
            this.method = 'patch';
            this.form.id = this.notes[index].id;
            this.form.title = this.notes[index].title;
            this.form.notable_type = this.notes[index].notable_type;
            this.form.notable_id = this.notes[index].notable_id;
            tinyMCE.get('note_content').setContent(this.notes[index].content);
            this.loadNotables();

            // if you edit a note and then click edit on another note, show the first note again
            this.$el.querySelector('span.hide')?.classList.remove('hide');

            this.$nextTick(function () {
                this.moveNoteEditor(this.form.id);
            })
        },
        viewNote(index) {
            this.$modal.show('note-show-modal', { 'note': this.notes[index] });
        },
        moveNoteEditor(id) {
            let editor = this.$el.getElementsByClassName('note_editor_selector')[0];
            let placeholder = this.$el.getElementsByClassName("note_editor_placeholder_" + id)[0];
            this.$el.getElementsByClassName("note_editor_hide_placeholder_" + id)[0].classList.add('hide');
            editor.parentNode.removeChild(editor);
            placeholder.appendChild(editor);
            this.$initTinyMCE(
                [
                    "autolink link example"
                ],
                {
                    'referenceable_type': 'App\\Note',
                    'referenceable_id': this.form.id,
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id,
                }
            );
        },
        loadNotes(type, id) {
            if (type == 'all') {
                this.form.notable_type = false;
                this.form.notable_id = false;
            } else {
                this.form.notable_type = 'App\\' + type;
                this.form.notable_id = id;
            }
            if (typeof (type) !== 'undefined' || type !== 'all') {
                localStorage.setItem('notes_notable_type', type);
                localStorage.setItem('notes_notable_id', id);
            }
            if (this.edit === true) {
                this.loadNotables();
            }

            this.load();
        },
        async destroy(id, index) {
            axios.delete("/notes/"+id)
                .then(res => { // Tell the parent component we've added a new task and include it
                    this.notes.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        diffForHumans(date) {
            return moment(date).locale('de').fromNow();
        },
        formatTime(timestamp) {
            let time = timestamp;
            if (this.timeFormatDiffForHumans === true) {
                time = this.diffForHumans(timestamp);
            }
            return time;
        },
        toggleTimestampFormatDiffForHumans() {
            this.timeFormatDiffForHumans = !this.timeFormatDiffForHumans;
        },
        showNote(item) {
            let show = false;

            if (item.title.toLowerCase().indexOf(this.search.toLowerCase()) !== -1
                || item.content.toLowerCase().indexOf(this.search.toLowerCase()) !== -1
                || this.search.length < 3) {
                show = true;
            }

            if (typeof(item.notable) !== 'undefined') {
                if (item.notable !== null) {
                    if (item.notable_type === 'App\\User') {
                        if (item.notable.firstname.toLowerCase().indexOf(this.search.toLowerCase()) !== -1
                            || item.notable.lastname.toLowerCase().indexOf(this.search.toLowerCase()) !== -1) {
                            show = true;
                        }
                    } else if (item.notable_type === 'App\\Group') {
                        if (item.notable.title.toLowerCase().indexOf(this.search.toLowerCase()) !== -1) {
                            show = true;
                        }
                    }
                }
            }
            return show;
        },
        prefillForm() {
            // needs to be filled after each submit, else these values will be null => throws 500
            this.form.notable_id = this.notable_id;
            this.form.notable_type = (localStorage.getItem('notes_notable_type') != null &&  localStorage.getItem('notes_notable_type') != 'all')
                ? 'App\\' + localStorage.getItem('notes_notable_type')
                : this.notable_type;
        },
    },
    mounted() {
        this.prefillForm();

        if (this.show_tabs === false) {
            localStorage.removeItem('notes_notable_type');
            localStorage.removeItem('notes_notable_id');
        }

        this.load();
        this.$initTinyMCE(
            [
                "autolink link example"
            ],
            {
                'eventHubCallbackFunction': 'insertContent',
                'eventHubCallbackFunctionParams': this.component_id,
            }
        );
    },
    components: {
        NoteShowModal,
    }
}
</script>
<style>
.note-content p {
    /* truncates text down to 1 line */
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
