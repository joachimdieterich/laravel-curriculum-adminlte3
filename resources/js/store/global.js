import {defineStore} from "pinia";

export const useGlobalStore = defineStore('global', {
            'generate-certificate-modal': {},
    state: () => ({
        global: [],
        modals: {
            'absence-modal' : {},
            'certificate-modal' : {},
            'config-modal': {},
            'confirm-modal': {},
            'contact-modal' : {},
            'content-modal' : {},
            'content-subscription-modal' : {}, //todo: check if used
            'course-modal' : {},
            'curriculum-modal' : {},
            'grade-modal' : {},
            'group-modal' : {},
            'kanban-modal' : {},
            'lms-modal' : {},
            'logbook-modal' : {},
            'logbook-entry-modal' : {},
            'medium-preview-modal' : {},
            'period-modal' : {},
            'role-modal' : {},
            'subscribe-logbook-modal' : {},
            'subscribe-modal' : {},
            'subscribe-objective-modal' : {},
            'subscribe-user-modal' : {},
            'task-modal' : {},
            'user-modal' : {},
        }
    }),
    actions: {
        setItem(key, value){
            this.global[key] = value;
        },
        registerModal(title){
            console.log('register: ' + title);
            this.modals[title] = {
                show: false,
                params: [],
            };
            //console.log(this.modals);
        },
        showModal(title, params = null){
            this.modals[title] = {
                show: true,
                params: params
            };
            //console.log(this.modals[title]);
        },
        closeModal(title){
            //console.log(title);
            this.modals[title].show = false;
            //this.modals[title].params = [];
        },
        setModalParams(title, params){
            //console.log(title);
            this.modals[title].params = params;
        }
    },
    getters: {
        getItem(state) {
            return  (key) => {
                return global[key] ?? null;
            };
        },
        getModalParams (state) {
            return (title) => {
                return this.modal[title]?.params ?? null;
            };
        }
    },
});
