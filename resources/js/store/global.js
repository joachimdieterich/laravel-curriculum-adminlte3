import {defineStore} from "pinia";

export const useGlobalStore = defineStore('global', {
    state: () => ({
        global: [],
        modals: {
            /*'absence-modal' : {},
            'certificate-modal' : {},
            'config-modal': {},
            'confirm-modal': {},
            'contact-modal' : {},
            'content-modal' : {},
            'content-subscription-modal' : {},
            'course-modal' : {},
            'curriculum-modal' : {},
            'exam-modal' : {},
            'generate-certificate-modal' : {},
            'grade-modal' : {},
            'group-modal' : {},
            'kanban-modal' : {},
            'lms-modal' : {},
            'logbook-modal' : {},
            'logbook-entry-modal' : {},
            'map-modal' : {},
            'map-marker-modal' : {},
            'metadataset-modal' : {},
            'medium-modal' : {},
            'medium-preview-modal' : {},
            'navigator-modal' : {},
            'navigator-item-modal' : {},
            'organization-modal' : {},
            'organization-type-modal' : {},
            'owner-modal' : {},
            'period-modal' : {},
            'permission-modal' : {},
            'plan-modal' : {},
            'prerequisite-objective-modal' : {},
            'reference-objective-modal' : {},
            'role-modal' : {},
            'set-achievements-modal':{},
            'subscribe-exam-modal' : {},
            'subscribe-logbook-modal' : {},
            'subscribe-modal' : {},
            'subscribe-objective-modal' : {},
            'subscribe-user-modal' : {},
            'terminal-objective-modal' : {},
            'task-modal' : {},
            'user-modal' : {},
            'videoconference-modal' : {},*/
        },
        media: [],
        showSearchbar: false,
        searchTagModelContext: null,
        mediumModalParams: {
            show: false,
            currentStatus: 0, // == STATUS_INITIAL
            accept: '',
            callback: 'medium-added',
            callbackId: null,
            public: 0,
            repository: 'local',
            subscribable_type: null,
            subscribable_id: null,
            subscribeSelected: false,
        },
        selectedMedia: [],
    }),
    actions: {
        setItem(key, value) {
            this.global[key] = value;
        },
        registerModal(title) {
            this.modals[title] = {
                show: false,
                lock: false, // used to not reset modal-params, when more than 1 modal is showing
                params: [],
            };
        },
        showModal(name, params = null) {
            this.modals[name] = {
                show: true,
                lock: false,
                params: params,
            };
        },
        closeModal(name) {
            this.modals[name].show = false;
            this.modals[name].lock = false;
        },
        lockModal(name) {
            this.modals[name].lock = true;
        },
        setModalParams(name, params) {
            this.modals[name].params = params;
        },
        addToMedia(item) {
            let index = this.media.findIndex(
                i => i.media === item.media
            );

            if (index !== -1){
                this.media[index] = item;
            } else {
                this.media.push(item);
            }
        },
        removeFromMedia(item) {
            let index = this.media.findIndex(
                i => i.media === item.media
            );

            if (index !== -1) {
                // nothing to do
            } else {
                this.media.splice(index, 1);
            }
        },
        setSelectedMedia(selection) {
            Object.assign(this.selectedMedia, selection);
        },
        addSelectedMedia(selection) {
            let index = this.selectedMedia.findIndex(
                i => i.id === selection.id
            );

            if (index === -1) this.selectedMedia.push(selection);
            else this.selectedMedia.splice(index, 1);
        },
        resetSelectedMedia() {
            this.selectedMedia = [];
        }
    },
    getters: {
        getItem(state) {
            return  (key) => {
                return this.global[key] ?? null;
            };
        },
        getModalParams (state) {
            return (title) => {
                return this.modal[title]?.params ?? null;
            };
        }
    },
});