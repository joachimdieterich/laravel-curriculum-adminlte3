<template>
    <div class="px-3">
        <ul class="nav nav-tabs">
            <li v-if="show_tabs === true"
                class="nav-item small"
                @click="setGlobalStorage('#note_view', '#note_view_all')"
            >
                <a class="nav-link show link-muted"
                   :class="getGlobalStorage('#note_view', '#note_view_all', 'active', true)"
                   href="#all"
                   @click="loadNotes('all')"
                >
                    <i class="fa fa-sticky-note pe-1"></i>
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
                >
                    <i class="fa fa-user pe-1"></i>
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
                >
                    <i class="fa fa-users pe-1"></i>
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
                >
                    <i class="fa fa-bullseye pe-1"></i>
                    <span v-if="help">{{ trans('global.achievement.title') }}</span>
                </a>
            </li>
            <li v-permission="'note_create'"
                class="nav-item ms-auto small pull-right">
                <a class="nav-link show link-muted"
                   v-if="edit === false"
                   @click="toggleEdit()"
                >
                    <i class="fa fa-plus pe-1"></i>
                </a>
            </li>
        </ul>

        <div class="bg-white border border-top-0 mb-3 rounded-bottom-3">
            <div :class="[edit ? 'note_editor_selector text-black': 'd-none']">
                <div class="mb-3">
                    <label for="note-title" class="form-label">{{ trans('global.task.fields.title') }} *</label>
                    <input
                        id="note-title"
                        type="text"
                        class="form-control"
                        maxlength="191"
                        v-model.trim="form.title"
                        :placeholder="trans('global.task.fields.title')"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="note-content" class="form-label">{{ trans('global.note_text') }}</label>
                    <Editor
                        id="note-content"
                        licenseKey="gpl"
                        :init="tinyMCE"
                        v-model="form.content"
                    />
                </div>
                <button
                    class="btn btn-primary float-end"
                    :disabled="!form.title"
                    @click="submit"
                >
                    {{ trans('global.save') }}
                </button>
                <button
                    class="btn btn-light float-end"
                    @click="toggleEdit"
                >
                    {{ trans('global.cancel') }}
                </button>

                <div class="clearfix border-bottom pb-2"></div>
            </div>

            <div v-for="(item,index) in notes"
                :value="item.id"
                class="p-2"
            >
                <div class="text-secondary ms-0">
                    <span :class="'note_editor_hide_placeholder_' + item.id">
                        <span class="d-flex align-items-center">
                            <span v-if="item.notable">
                                <a v-if="item.notable_type === 'App\\User'"
                                    :href="'/users/' + item.notable.id"
                                    class="text-bold text-decoration-none text-gray-dark"
                                >
                                    {{ item.notable.firstname }} {{ item.notable.lastname }}
                                </a>
                                <a v-else-if="item.notable_type === 'App\\Group'"
                                    :href="'/groups/' + item.notable.id"
                                    class="text-bold text-gray-dark"
                                >
                                    {{ item.notable.title }}
                                </a>
                                <a v-else-if="item.notable_type === 'App\\Achievement'"
                                    :href="'/enablingObjectives/' + item.notable.referenceable_id"
                                    class="text-bold text-gray-dark"
                                >
                                    <i class="fa fa-link me-1"></i>
                                </a>
                                <a v-else
                                    lass="text-bold text-gray-dark"
                                >
                                    {{ item.notable.title }}
                                </a>
                            </span>

                            <span class="text-gray-dark">{{ item.title }}</span>

                            <small class="ms-2 badge text-bg-info">{{ trans('global.' + item.notable_type) }}</small>

                            <span class="d-flex gap-2 ms-2">
                                <button
                                    type="button"
                                    class="btn btn-icon text-secondary"
                                    @click="editNote(index)"
                                >
                                    <i class="fa fa-pencil-alt"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-icon text-danger"
                                    @click="destroy(item.id, index)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </span>

                        </span>

                        <div>
                            <small class="d-flex gap-2 text-muted">
                                <span>
                                    <i class="fa fa-plus-circle"></i> {{ item.created_at }}
                                </span>
                                <span v-if="item.created_at != item.updated_at">
                                    <i class="fas fa-pencil-alt"></i> {{ item.updated_at }}
                                </span>
                            </small>
                        </div>

                        <div v-html="item.content"></div>
                    </span>
                    <span :class="'note_editor_placeholder_'+item.id"></span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';

export default {
    components: { Editor },
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
            notes: {},
            form: new Form({
                id: null,
                title: '',
                content: '',
                notable_type: '' ,
                notable_id: null,
            }),
            edit: false,
            help: false,
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "lists", "code", "autoresize",
                ],
                {
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id,
                },
                "bold underline cursive | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code",
                ""
            ),
            notables: {},
        }
    },
    mounted() {
        this.globalStore['showSearchbar'] = true;

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

        this.$eventHub.on('filter', filter => {
            this.filterNotes(filter.searchString);
        });
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
            } catch(error) {
                //console.log('loading failed')
            }
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

            this.form.submit(method, '/notes' + currentPath)
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
            tinyMCE.get('note-content').setContent(this.notes[index].content);
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
        filterNotes(search) {
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
}
</script>