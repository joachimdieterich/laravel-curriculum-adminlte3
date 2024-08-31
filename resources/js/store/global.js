import {defineStore} from "pinia";

export const useGlobalStore = defineStore('global', {
    state: () => ({
        global: [],
        modals: []
    }),
    actions: {
        setItem(key, value){
            this.global[key] = value;
        },
        registerModal(title){
            console.log(title);
            this.modals[title] = {
                modal: title,
                show: false
            };
            //console.log(this.modals);
        },
        showModal(title, params = null){
            this.modals[title].show = true;
            this.modals[title].params = params;
            //console.log(this.modals);
        },
        closeModal(title){
            //console.log(title);
            this.modals[title].show = false;

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
                let modal = this.modals.find(g => g.modal === title);
                console.log(title);
                return modal?.params ?? null;
            };
        }
    },
});
