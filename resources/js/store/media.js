import {defineStore} from "pinia";

export const useMediumStore = defineStore('medium', {
    state: () => ({
        media: [],
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
        selectedMedia:[]

    }),
    actions: {
        addToMedia(item) {
            let index = this.media?.findIndex(
                i => i.media === item.media
            );

            if (index !== -1){
                this.media[index] = item;
            } else {
                this.media.push(item);
            }
            //console.log(this.media);
        },
        removeFromMedia(item) {
            let index = this.media?.findIndex(
                i => i.media === item.media
            );

            if (index !== -1){
                // nothing to do
            } else {
                this.media.splice(index, 1);
            }
            //console.log(this.media);
        },
        setMessage(message){
            this.mediumModalParams.message = message;
        },
        setShowMediumModal(show){
            this.mediumModalParams.show = show;
        },
        setMediumModalParams(params){ // reset if no params are given
            this.mediumModalParams.show = params?.show ?? false, // == STATUS_INITIAL
            this.mediumModalParams.currentStatus = params?.currentStatus ?? 0, // == STATUS_INITIAL
            this.mediumModalParams.accept = params?.accept ?? '',
            this.mediumModalParams.callback =  params?.callback ?? 'medium-added',//previous eventHubCallbackFunction
            this.mediumModalParams.callbackId =  params?.callbackId ?? null, //previous eventHubCallbackParams
            this.mediumModalParams.public = params?.public ?? 0,
            this.mediumModalParams.repository = params?.repository ?? 'local',
            this.mediumModalParams.subscribable_type = params?.subscribable_type ?? null,
            this.mediumModalParams.subscribable_id = params?.subscribable_id ?? null,
            this.mediumModalParams.subscribeSelected = params?.subscribeSelected ?? false
            this.mediumModalParams.message = params?.message ?? ''
        },
        setSelectedMedia(selection){
            this.selectedMedia = selection;
        }
    },
    getters: { //can be called directly
        /*getShowMediumModal(state){
            return state.mediumModalParams.show;
        },
        getMedia(state) {
            return  state.media;
        },*/
    },
});
