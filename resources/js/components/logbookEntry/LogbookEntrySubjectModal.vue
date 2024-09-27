<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ trans('global.logbookEntry.subject') }}
                    </h3>
                    <div class="card-tools">
                        <button type="button"
                                class="btn btn-tool"
                                @click="globalStore?.closeModal($options.name)">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                        <Select2
                            :id="'subject_' + component_id "
                            :name="'subject_' + component_id "
                            option_id="id"
                            url="/subjects"
                            model="subject"
                            :selected="this.form.subject_id"
                            @selectedValue="(id) => {
                            this.form.subject_id = id;
                        }"
                        >
                        </Select2>
                    </div>
                </div>

                <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="logbook-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="logbook-save"
                         class="btn btn-primary"
                         @click="submit(method)" >
                         {{ trans('global.save') }}
                     </button>
                </span>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";
import {useGlobalStore} from "../../store/global";

export default {
    name: 'logbook-entry-subject-modal',
    components:{
        Select2
    },
    props: {},
    setup () {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/logbookEntries',
            form: new Form({
                'id': '',
                'subject_id':'',
                'title': '',
            }),
        }
    },
    methods: {
        submit(){
            axios.patch(this.url + '/' + this.form.id + '/setSubject', this.form)
                .then(r => {
                    this.$eventHub.emit('updateSubjectBadge', {
                        entry_id: this.form.id,
                        subject_id:  r.data.id,
                        title:  r.data.title,
                    });
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (mutation.events.key === this.$options.name){
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined'){
                    this.form.populate(params);
                }
            }
        });
    },
}
</script>
