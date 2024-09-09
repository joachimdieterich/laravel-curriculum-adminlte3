import {defineStore} from "pinia";

export const useGlobalStore = defineStore('global', {
            'generate-certificate-modal': {},
    state: () => ({
        global: [],
        modals: {
            'absence-modal' : {},
            'certificate-modal' : {},
            'config-modal': {},
            'contact-modal' : {},
            'content-modal' : {},
            'content-subscription-modal' : {}, //todo: check if used
            'course-modal' : {},
            'lms-modal' : {},
            'medium-preview-modal' : {},
            'subscribe-objective-modal' : {},
            'task-modal' : {}
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
            this.modals[title].show = true;
            this.modals[title].params = params;
            console.log(this.modals[title]);
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
