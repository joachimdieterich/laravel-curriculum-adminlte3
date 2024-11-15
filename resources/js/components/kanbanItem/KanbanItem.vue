<template>
    <div
        :id="'item-' + item.id"
        class="card"
    >
        <div
            class="card-header p-0"
            :style="{ color: textColor }"
            data-toggle="collapse"
            :data-target="'#item-' + item.id + ' > .card-body'"
            aria-expanded="true"
        >
            <div
                class="card-header-title pl-3 py-2 w-100"
                :style="{ backgroundColor: item.color }"
                style="max-width: 100%; padding-right: 50px; border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem;"
            >
                {{ item.title }}
                <i class="fa fa-angle-up"></i>
                <div style="font-size: .5rem">
                    {{ item.created_at }}
                </div>
            </div>
            <div
                class="card-tools position-absolute"
                style="top: 8px; right: 16px;"
            >
                <div v-if="$userId == kanban_owner_id
                        || (editable && $userId == item.owner_id)
                        || (editable && item.editable && onlyEditOwnedItems == false)"
                    class="float-right py-0 px-2 pointer"
                    :id="'kanbanItemDropdown_'+index"
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
                        <button
                            :name="'kanbanItemEdit_'+index"
                            class="dropdown-item text-secondary py-1"
                            @click="edit()"
                        >
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.kanbanItem.edit') }}
                        </button>
                        <button
                            v-permission="'external_medium_create'"
                            class="dropdown-item text-secondary py-1"
                            :name="'kanbanItemAddMedia_'+index"
                            @click="addMedia()"
                        >
                            <i class="fa fa-folder-open mr-2"></i>
                            {{ trans('global.medium.title_singular') }}
                        </button>
                        <div v-if="$userId == item.owner_id || $userId == kanban_owner_id">
                            <hr class="my-1">
                            <button
                                v-permission="'kanban_delete'"
                                class="dropdown-item py-1 text-red"
                                :name="'kanbanItemDelete_'+index"
                                @click="confirmItemDelete()"
                            >
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.kanbanItem.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="(!item.locked || $userId == item.owner_id) || $userId == kanban_owner_id"
                    class="float-right py-0 px-1 mx-1 handle pointer"
                >
                    <i class="fa fa-arrows-up-down-left-right"
                       :style="{ 'text-color': textColor }"
                    ></i>
                </div>
            </div>
        </div>

        <div class="card-body p-0 collapse show">
            <div>
                <div class="text-muted small px-3 py-2">
                    <span v-if="item.replace_links">
                        <HtmlRenderer
                            :html-content="item.description ?? '</br>'"
                        ></HtmlRenderer>
                    </span>
                    <span v-else v-dompurify-html="item.description ?? '</br>'"></span>
                </div>
            </div>
            <mediaCarousel
                v-if="item.media_subscriptions.length > 0"
                class="clearfix"
                :subscriptions="item.media_subscriptions"
                :width="width -16"
            ></mediaCarousel>
        </div>
        <div v-if="item.due_date != null
                && (item.visible_from != null || item.visible_until != null)"
            class="card-footer px-3 py-2"
            :class="{ 'border-top-0': item.description === null }"
        >
            <div class="w-100">
                <div class="due-date pull-left">{{ postDate() }}</div>
                <span v-if="expired"
                    class="pull-right badge badge-secondary"
                >
                    {{ trans('global.kanbanItem.expired') }}
                </span>
                <div class="due-date pull-left">
                    {{ trans('global.visibility')}} {{ trans('global.timeFrom')}}:  {{ diffForHumans(item.visible_from)}} {{ trans('global.timeTo')}}: {{ diffForHumans(item.visible_until) }}
                </div>
            </div>
        </div>

        <div
            class="card-footer px-3 py-2"
            :class="{ 'border-top-0': item.description === null }"
        >
            <div class="d-flex align-items-center">
               <avatar
                    :key="item.id + '_editor_' +item.owner.id"
                    :title="item.owner.firstname + ' ' + item.owner.lastname"
                    :username="item.owner.username"
                    :firstname="item.owner.firstname"
                    :lastname="item.owner.lastname"
                    :size="25"
                    class="contacts-list-img"
                    data-toggle="tooltip"
                ></avatar>
                <avatar
                    v-if="editors != null && $userId != 8"
                    v-for="(editor_user, index) in editors"
                    :key="item.id + '_editor_' + index"
                    :title="editor_user.firstname + ' ' + editor_user.lastname"
                    :username="editor_user.username"
                    :firstname="editor_user.firstname"
                    :lastname="editor_user.lastname"
                    :size="25"
                    class="contacts-list-img"
                    data-toggle="tooltip"
                ></avatar>

                <span class="d-flex flex-fill"></span>
                <div v-if="commentable"
                    class="mr-2 px-1 pointer"
                    @click="openComments"
                >
                    <i class="far fa-comments"></i>
                    <span v-if="item.comments.length > 0"
                        class="comment-count mt-1 small bg-success"
                    >
                        {{ item.comments.length }}
                    </span>
                </div>
                <Reaction
                    :model="item"
                    reaction="like"
                    url="/kanbanItems"
                />
            </div>
        </div>

        <Comments
            v-if="show_comments"
            :comments="item.comments"
            :model="item"
            :kanban_owner_id="kanban_owner_id"
            url="/kanbanItemComment"
        />

        <Teleport to="body">
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.kanbanItem.delete')"
                :description="trans('global.kanbanItem.delete_helper')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.delete();
                }"
            ></ConfirmModal>
        </Teleport>
    </div>
</template>

<script>
import DatePicker from 'vue3-datepicker';
import mediaCarousel from '../media/MediaCarousel.vue';
import avatar from '../uiElements/Avatar.vue';
import Reaction from '../reaction/Reaction.vue';
import Comments from '../kanban/Comments.vue';
import moment from 'moment';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import HtmlRenderer from "../uiElements/HtmlRenderer.vue";
import {useGlobalStore} from "../../store/global";
import {useMediumStore} from "../../store/media.js";

export default {
    props: {
        item: Object,
        index: String,
        width: Number,
        commentable: false,
        onlyEditOwnedItems: false,
        likable: true,
        editable: false,
        replace_links: true,
        kanban_owner_id: {
            type: Number,
            default: null
        }
    },
    setup() { //use database store
        const globalStore = useGlobalStore();
        const mediumStore = useMediumStore();
        return {
            globalStore,
            mediumStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            showConfirm: false,
            currentItem : {},
            new_media: null,
            show_comments: false,
            expired: false,
            editors: null,
        };
    },
    computed: {
        textColor: function() {
            if (this.item.color == "" || this.item.color == null) return;
            return this.$textcolor(this.item.color, '#333333');
        },
        diffForHumans: function(date) {
            if (date == null) {
                return '\u221E';
            } else {
                return moment(date).locale('de').fromNow();
            }
        },
    },
    methods: {
        confirmItemDelete(item) {
            this.currentItem = item;
            this.showConfirm = true;
        },
        delete() {
            axios.delete("/kanbanItems/" + this.item.id)
                .then(() => {
                    this.$eventHub.emit("kanban-item-deleted", this.item);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        edit() {
            this.globalStore?.showModal('kanban-item-modal', {
                item: this.item,
                method: 'patch',
            });
        },
        openComments() {
            this.show_comments = !this.show_comments;
        },
        addMedia() {
            this.mediumStore.setMediumModalParams(
                {
                    'show': true,
                    'subscribeSelected': true,
                    'subscribable_type': 'App\\\KanbanItem',
                    'subscribable_id': this.item.id,
                    'public': 0,
                    'callbackId': this.component_id
                });
        },
        reload() { //after media upload
            axios.get("/kanbanItems/" + this.item.id)
                .then(res => {
                    this.$eventHub.emit("kanban-item-updated", res.data.message);
                    //this.item = res.data.message;
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
        getEditors() { //after media upload
            axios.get("/kanbanItems/" + this.item.id + "/editors")
                .then(res => {
                    this.editors = res.data.editors;
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
                minute: '2-digit'
            };

            this.expired = new Date() > date;

            return date.toLocaleString([], dateFormat);
        },
    },
    mounted() {
        this.getEditors();
        //this.due_date = this.item.due_date;
        this.$eventHub.on('reload_kanban_item', (e) => {
            if (this.item.id == e.id) {
                this.reload();
            }
        });
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
    },
    components: {
        HtmlRenderer,
        Comments,
        Reaction,
        mediaCarousel,
        avatar,
        DatePicker,
        ConfirmModal,
    }
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
.card-header-title:hover { filter: brightness(90%); }
.fa-angle-up { transition: 0.4s transform; }
.collapsed .fa-angle-up { transform: rotate(-180deg); }
</style>