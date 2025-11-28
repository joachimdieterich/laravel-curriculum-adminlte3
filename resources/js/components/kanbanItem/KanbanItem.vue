<template>
    <div
        :id="'item-' + item.id"
        class="card"
        :style="!item.visibility || hidden ? 'opacity: 0.7;' : ''"
        tabindex="-1"
    >
        <div
            class="card-header p-0"
            :class="collapse_items && 'collapsed'"
            :style="{ color: textColor }"
            data-toggle="collapse"
            :data-target="'#item-' + item.id + ' > .card-body'"
            aria-expanded="true"
        >
            <div
                class="card-header-title pl-3 py-2"
                :style="{ backgroundColor: item.color }"
            >
                {{ item.title }}
                <i class="fa fa-angle-up d-print-none"></i>
                <div style="font-size: 10px;">
                    {{ item.created_at }}
                </div>
            </div>
            <div
                class="card-tools d-print-none position-absolute"
                style="top: 8px; right: 16px;"
            >
                <div v-if="edit_rights || copy_rights || delete_rights"
                    :id="'kanbanItemDropdown_' + index"
                    class="float-right py-0 px-2 pointer"
                    style="background-color: transparent;"
                    data-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="fas fa-ellipsis-v"
                       :style="{ 'text-color': textColor }"
                    ></i>
                    <div
                        class="dropdown-menu"
                        x-placement="top-start"
                    >
                        <div v-if="edit_rights">
                            <button
                                :name="'kanbanItemEdit_' + index"
                                class="dropdown-item text-secondary py-1"
                                @click="edit()"
                            >
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.kanbanItem.edit') }}
                            </button>
                            <button
                                v-permission="'external_medium_create'"
                                class="dropdown-item text-secondary py-1"
                                :name="'kanbanItemAddMedia_' + index"
                                @click="addMedia()"
                            >
                                <i class="fa fa-folder-open mr-2"></i>
                                {{ trans('global.medium.title_singular') }}
                            </button>
                        </div>

                        <div v-if="copy_rights">
                            <button
                                name="kanbanItemCopy"
                                class="dropdown-item text-secondary py-1"
                                @click="confirmCopy()"
                            >
                                <i class="fa fa-copy mr-2"></i>
                                {{ trans('global.kanbanItem.copy') }}
                            </button>
                        </div>

                        <div v-if="delete_rights">
                            <hr class="my-1">
                            <button
                                v-permission="'kanban_delete'"
                                class="dropdown-item py-1 text-red"
                                :name="'kanbanItemDelete_' + index"
                                @click="confirmDeletion()"
                            >
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.kanbanItem.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="(!item.locked || $userId == item.owner_id) || $userId == kanban_owner_id"
                    class="float-right py-0 px-1 mx-1 handle pointer"
                    @click.stop
                >
                    <span class="position-relative"
                          :style="{ 'text-color': textColor }">
                        <i v-if="editable"
                           class="fa fa-arrows-up-down-left-right"
                        ></i>
                        <i v-if="item.locked"
                           class="fa fa-lock text-muted position-absolute"
                           style="left: 8px; top: 10px; cursor: not-allowed;"
                        ></i>
                    </span>
                </div>
            </div>
        </div>

        <div
            class="card-body p-0 collapse"
            :class="!collapse_items && 'show'"
        >
            <div style="overflow-x: auto;">
                <div class="text-muted small px-3 py-2">
                    <span v-if="item.replace_links">
                        <HtmlRenderer :html-content="item.description.length > 0 ? item.description : '</br>'"/>
                    </span>
                    <span v-else v-html="item.description.length > 0 ? item.description : '</br>'"></span>
                </div>
            </div>
            <MediaCarousel v-if="item.media_subscriptions.length > 0"
                class="clearfix"
                :subscriptions="item.media_subscriptions"
                :width="width - 16"
            />
        </div>

        <div v-if="item.due_date || (item.visibility && (item.visible_from || item.visible_until))"
            class="card-footer px-3 py-2"
            :class="{ 'border-top-0': item.description === null }"
        >
            <div class="w-100">
                <div v-if="item.due_date">
                    <div class="due-date pull-left">{{ trans('global.due_at') }}: {{ postDate() }}</div>
                    <span v-if="expired"
                        class="pull-right badge badge-secondary"
                    >
                        {{ trans('global.kanbanItem.expired') }}
                    </span>
                </div>
                <div v-if="item.visibility && (item.visible_from || item.visible_until)">
                    <div class="due-date pull-left">
                        {{ trans('global.visibility') }}
                        <span v-if="item.visible_from && new Date() < new Date(item.visible_from)">{{ diffForHumans(item.visible_from) }}</span>
                        <span v-if="new Date() > new Date(item.visible_from)">{{ trans('global.timeTo') }} {{ diffForHumans(item.visible_until) }}</span>
                    </div>
                    <span v-if="hidden"
                        class="pull-right badge badge-secondary"
                    >
                        {{ trans('global.hidden') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex align-items-center px-3 py-2">
            <Avatar
                :key="item.id + '_editor_' + item.owner.id"
                :title="item.owner.firstname + ' ' + item.owner.lastname"
                :username="item.owner.username"
                :firstname="item.owner.firstname"
                :lastname="item.owner.lastname"
                :size="25"
                class="contacts-list-img o"
                data-toggle="tooltip"
            />
            <Avatar v-if="editors != null"
                v-for="(editor_user, index) in editorsWithoutOwner"
                :key="item.id + '_editor_' + index"
                :title="editor_user.firstname + ' ' + editor_user.lastname"
                :username="editor_user.username"
                :firstname="editor_user.firstname"
                :lastname="editor_user.lastname"
                :size="25"
                class="contacts-list-img"
                data-toggle="tooltip"
            />

            <div class="d-flex ml-auto">
                <button v-if="commentable"
                    class="btn btn-icon px-2 py-1 mr-2"
                    :title="show_comments ? trans('global.hide_comments') : trans('global.show_comments')"
                    data-toggle="collapse"
                    :data-target="'#comments_' + item.id"
                    aria-expanded="false"
                    @click="show_comments = !show_comments"
                >
                    <i class="far fa-comments"></i>
                    <span v-if="item.comments.length > 0"
                        class="comment-count bg-success"
                    >
                        {{ item.comments.length }}
                    </span>
                </button>
                <Reaction
                    :model="item"
                    reaction="like"
                    url="/kanbanItems"
                />
            </div>
        </div>

        <Comments v-if="commentable"
            :websocket="websocket"
            :comments="item.comments"
            :model="item"
            :kanban_owner_id="kanban_owner_id"
        />
    </div>
</template>
<script>
import MediaCarousel from '../media/MediaCarousel.vue';
import Avatar from '../uiElements/Avatar.vue';
import Reaction from '../reaction/Reaction.vue';
import Comments from '../kanban/Comments.vue';
import HtmlRenderer from "../uiElements/HtmlRenderer.vue";
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        item: {
            type: Object,
            default: null,
        },
        index: {
            type: String,
            default: null,
        },
        width: {
            type: Number,
            default: null,
        },
        commentable: {
            type: Boolean,
            default: false,
        },
        only_edit_owned_items: {
            type: Boolean,
            default: false,
        },
        editable: {
            type: Boolean,
            default: false,
        },
        collapse_items: {
            type: Boolean,
            default: false,
        },
        allow_copy: {
            type: Boolean,
            default: false,
        },
        replace_links: {
            type: Boolean,
            default: true,
        },
        kanban_owner_id: {
            type: Number,
            default: null,
        },
        websocket: {
            type: Boolean,
            default: false,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            currentItem : {},
            edit_rights: false,
            copy_rights: false,
            delete_rights: false,
            new_media: null,
            show_comments: false,
            expired: false, // due date expired
            editors: null,
            editorsWithoutOwner: null,
        };
    },
    computed: {
        textColor: function() {
            if (this.item.color == "" || this.item.color == null) return;
            return this.$textcolor(this.item.color, '#333333');
        },
        hidden: function() { // check if item is hidden based on visible-from/to dates
            return (this.item.visible_from != null || this.item.visible_until != null)
                && (new Date() < new Date(this.item.visible_from) || new Date() > new Date(this.item.visible_until));
        }
    },
    methods: {
        diffForHumans(date) {
            if (date == null) {
                return '\u221E';
            } else {
                return moment(date).locale(window.navigator.language).fromNow();
            }
        },
        confirmCopy() {
            this.$eventHub.emit('kanban-show-copy', {
                id: this.item.id,
                type: 'item',
            });
        },
        confirmDeletion() {
            this.$eventHub.emit('kanban-show-delete', {
                id: [this.item.id, this.item.kanban_status_id],
                type: 'item',
            });
        },
        edit() {
            this.globalStore?.showModal('kanban-item-modal', {
                item: this.item,
                method: 'patch',
            });
        },
        addMedia() {
            this.globalStore?.showModal('medium-modal', {
                show: true,
                subscribeSelected: true,
                subscribable_type: 'App\\KanbanItem',
                subscribable_id: this.item.id,
                public: 0,
                callbackId: this.component_id,
            });
        },
        reload() { //after media upload
            axios.get("/kanbanItems/" + this.item.id)
                .then(res => {
                    this.$eventHub.emit('kanban-item-updated-' + this.item.kanban_status_id, res.data.message);
                    //this.item = res.data.message;
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        getEditors() { //after media upload
            // I don't know why the editors shouldn't be shown to guests
            if (this.$userId == null || this.$userId == 8) return;

            axios.get("/kanbanItems/" + this.item.id + "/editors")
                .then(res => {
                    this.editors = res.data;
                    this.editorsWithoutOwner = this.editors.filter(e => e.id !== this.item.owner.id);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        postDate() {
            if (this.item.due_date == null) return undefined;

            const date = new Date(this.item.due_date.replace(/-/g, "/"));
            const dateFormat = {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            };

            this.expired = new Date() > date;

            return date.toLocaleString([], dateFormat);
        },
        startWebsocket() {
            if (this.websocket === true) {
                this.$echo
                    .channel('App.KanbanItem.' + this.item.id)
                    .listen('.KanbanItemUpdated', (payload) => {
                        this.$eventHub.emit('kanban-item-updated-' + this.item.kanban_status_id, payload.model);

                        this.getEditors();
                    })
                    .listen('.KanbanItemDeleted', (payload) => {
                        this.$eventHub.emit('kanban-item-deleted-' + this.item.kanban_status_id, payload.model);
                    })
                ;
            }
        },
        stopWebsocket() {
            if (this.websocket === true) {
                this.$echo.leave('App.KanbanItem.' + this.item.id);
            }
        },
    },
    mounted() {
        this.startWebsocket();

        this.edit_rights =
            this.$userId == this.kanban_owner_id
            || this.checkPermission('is_admin')
            // (edit = true and item-owner) or (edit = true and item-edit = true and everyone can edit)
            || (this.editable && (this.$userId == this.item.owner_id || (this.item.editable && !this.only_edit_owned_items)));

        this.copy_rights = this.allow_copy && this.editable;

        this.delete_rights =
            this.$userId == this.kanban_owner_id
            || this.checkPermission('is_admin')
            || this.$userId == this.item.owner_id;

        this.getEditors();
        this.$eventHub.on('filter', (filter) => {
            // always case insensitive
            const content = this.$el.innerText.toLowerCase();
            const search = filter.toLowerCase();

            this.$el.style.display = content.includes(search)
                ? 'flex'
                : 'none';
        });

        this.$eventHub.on('medium-added', (e) => {
            if (this.component_id == e.id) {
                this.reload();
            }
        });

        this.$nextTick(() => {
            MathJax.typeset();
        });
    },
    unmounted() {
        this.stopWebsocket();
    },
    watch: {
        'item.description': function() {
            this.$nextTick(() => {
                MathJax.typeset();
            });
        },
    },
    components: {
        HtmlRenderer,
        Comments,
        Reaction,
        MediaCarousel,
        Avatar,
    },
}
</script>
<style scoped>
.due-date {
    color: #6c757d;
    font-size: 12px;
    font-weight: 600;
}
.badge {
    border: 1px solid #dc3545;
    background-color: #fff;
    color: #dc3545;
    font-size: 10px;
    line-height: 11px;
    vertical-align: middle;
}
.card-header-title {
    transition: filter 0.25s;
    padding-right: 3.5rem;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}
.card-header-title:hover { filter: brightness(90%); }
</style>