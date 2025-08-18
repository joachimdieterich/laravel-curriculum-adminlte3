<template>
    <div>
        <ul class="nav nav-tabs ">
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setGlobalStorage('#note_view', '#note_view_all')"
            >
                <a class="nav-link show link-muted"
                   :class="getGlobalStorage('#note_view', '#note_view_all', 'active', true)"
                   href="#all"
                   @click="loadNotes('all')"
                   data-toggle="tab">
                    <i class="fa fa-sticky-note pr-1"></i>
                    <span v-if="help">{{ trans('global.note.title') }}</span>
                </a>
            </li>
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setGlobalStorage('#note_view', '#note_view_users')"
            >
                <a class="nav-link show link-muted"
                   :class="getGlobalStorage('#note_view', '#note_view_users')"
                   href="#users"
                   @click="loadNotes('User')"
                   data-toggle="tab">
                    <i class="fa fa-user pr-1"></i>
                    <span v-if="help">{{ trans('global.user.title') }}</span>
                </a>
            </li>
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setGlobalStorage('#note_view', '#note_view_groups')"
            >
                <a class="nav-link show link-muted"
                   :class="getGlobalStorage('#note_view', '#note_view_groups')"
                   href="#users"
                   @click="loadNotes('Group')"
                   data-toggle="tab">
                    <i class="fa fa-users pr-1"></i>
                    <span v-if="help">{{ trans('global.group.title') }}</span>
                </a>
            </li>
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setGlobalStorage('#note_view', '#note_view_achievements')"
            >
                <a class="nav-link show link-muted"
                   :class="getGlobalStorage('#note_view', '#note_view_achievements')"
                   href="#achievements"
                   @click="loadNotes('Achievement')"
                   data-toggle="tab">
                    <i class="fa fa-bullseye pr-1"></i>
                    <span v-if="help">{{ trans('global.achievement.title') }}</span>
                </a>
            </li>
            <li v-permission="'note_create'"
                class="nav-item ml-auto small pull-right">
                <a class="nav-link show link-muted"
                   v-if="edit === false"
                   @click="toggleEdit()">
                    <i class="fa fa-plus pr-1"></i>
                </a>
            </li>
        </ul>
        <div class="col-md-12 p-0">
            <div id="logbooks_filter" class="dataTables_filter mb-0 rounded-0">
                <label >
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search">
                </label>
            </div>
        </div>
        <div class="tab-content card-footer card-comments">
            <div class="tab-pane show active">
                <div :class="[edit ? 'note_editor_selector': 'd-none']">
                    <div v-if="notables.length > 0"
                         class="form-group "
                         :class="[(method == 'patch') ? 'd-none': '']">
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
                        <Editor
                            id="note_content"
                            name="note_content"
                            class="form-control"
                            licenseKey="gpl"
                            :init="tinyMCE"
                            :initial-value="form.content"
                        />
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

                <div class="card-comment"
                     v-for="(item,index) in notes" v-bind:value="item.id"
                     v-if="showNote(item)">
                    <div class="comment-text ml-0"
                         @mouseover="hover = item.id"
                         @mouseleave="hover = false"
                         >
                        <span :class="'note_editor_hide_placeholder_'+item.id">
                        <span >
                             <span v-if="item.notable">
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
                            <span class="text-gray-dark">{{item.title}}</span>
                            <small class="ml-2 badge badge-info">{{ trans('global.'+item.notable_type) }}</small>
                            <i v-if="hover == item.id"
                               class="text-muted p-1 fa fa-pencil-alt pointer"
                               style="font-weight: 900;"
                               @click="editNote(index)"></i>

                            <small class="text-muted float-right">
                                <i v-if="hover == item.id"
                                   class="text-danger p-1 fa fa-trash pointer"
                                   @click="destroy(item.id, index)"></i>
                                <a class="pointer link-muted text-decoration-none"
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
                            </span>
                            <span v-html="item.content"></span>
                        </span>
                        <span :class="'note_editor_placeholder_'+item.id"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';

export default {
    components: {
        Editor,
    },
    props: {
        notable_type: {
            type: String,
            default: null,
        },
        notable_id: {
            type: Number,
            default: null,
        },
        show_tabs: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            requestUrl: '/notes',
            notes: {},
            form: new Form({
                id: '',
                title: '',
                content: '',
                notable_type: '' ,
                notable_id: '',
            }),
            edit: false,
            hover: '',
            help: false,
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "autoresize",
                ],
                {
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id,
                }
            ),
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
            } catch(e) {
                console.log(e)
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
                this.$nextTick(function (){
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
        submit() {
            var method = this.method.toLowerCase();
            this.form.content = tinyMCE.get('note_content').getContent();

            tinyMCE.get('note_content').setContent('');

            let currentPath = '';
            if (method === 'patch') {
                currentPath =  '/' + this.form.id;
                this.method = 'post';
                const index = this.notes.findIndex(note => note.id === this.form.id); //remove note, it will be added after submit again
                this.notes.splice(index, 1);
            }

            this.form.submit(method, this.requestUrl + currentPath)
                .then(response => this.notes.unshift(response))
                .catch(response => console.log(response));

            this.toggleEdit();
        },
        toggleEdit() {
            this.edit = !this.edit;
            if (this.edit === true){
                this.loadNotables();
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

            this.$nextTick(function (){
                this.moveNoteEditor(this.form.id);
            })
        },
        moveNoteEditor(id) {
            let editor = this.$el.getElementsByClassName('note_editor_selector')[0];
            let placeholder = this.$el.getElementsByClassName("note_editor_placeholder_" + id)[0];
            let hidePlaceholder = this.$el.getElementsByClassName("note_editor_hide_placeholder_" + id)[0];
            editor.parentNode.removeChild(editor);
            hidePlaceholder.parentNode.removeChild(hidePlaceholder);
            placeholder.appendChild(editor);
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
        loadNotes(type, id) {
            if (type == 'all') {
                this.form.notable_type = false;
                this.form.notable_id = false;
            } else {
                this.form.notable_type = 'App\\' + type;
                this.form.notable_id = id;
            }
            if (typeof (type) !== 'undefined' || type !== 'all'){
                localStorage.setItem('notes_notable_type', type);
                localStorage.setItem('notes_notable_id', id);
            }
            if (this.edit === true){
                this.loadNotables();
            }

            this.load();
        },
        async destroy(id, index) {
            axios.delete("/notes/" + id)
                .then(res => { // Tell the parent component we've added a new task and include it
                    this.notes.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        diffForHumans : function (date) {
            return moment(date).locale(window.navigator.language).fromNow();
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

            if (item?.title.toLowerCase().indexOf(this.search.toLowerCase()) !== -1
                || item?.content.toLowerCase().indexOf(this.search.toLowerCase()) !== -1
                || this.search.length < 3){
                show = true;
            }

            if (typeof(item?.notable) !== 'undefined') {
                if (item?.notable !== null) {
                    if (item?.notable_type === 'App\\User') {
                        if (item?.notable.firstname.toLowerCase().indexOf(this.search.toLowerCase()) !== -1
                            || item?.notable.lastname.toLowerCase().indexOf(this.search.toLowerCase()) !== -1) {
                            show = true;
                        }
                    } else if (item?.notable_type === 'App\\Group') {
                        if (item?.notable.title.toLowerCase().indexOf(this.search.toLowerCase()) !== -1) {
                            show = true;
                        }
                    }
                }
            }
            return show;
        },
    },
    mounted() {
        this.form.notable_type = this.notable_type;
        this.form.notable_id = this.notable_id;

        if (this.show_tabs === false) {
            localStorage.removeItem('notes_notable_type');
            localStorage.removeItem('notes_notable_id');
        }
        if (localStorage.getItem('notes_notable_type') != null &&  localStorage.getItem('notes_notable_type') != 'all') {
            this.form.notable_type = 'App\\' + localStorage.getItem('notes_notable_type');
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
}
</script>