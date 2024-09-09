<template>
    <Transition name="modal">
        <div v-if="this.globalStore.modals['course-modal']?.show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.course.create') }}
                    </span>
                        <span v-if="method === 'patch'">
                        {{ trans('global.course.edit') }}
                    </span>
                    </h3>
                    <div class="card-tools">
                        <button type="button"
                                class="btn btn-tool"
                                @click="this.globalStore?.closeModal('course-modal')">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <Select2
                        id="curricula"
                        name="curricula"
                        url="/curricula"
                        model="curriculum"
                        option_id="id"
                        option_label="title"
                        :selected="this.form.curriculum_id"
                        @selectedValue="(id) => {
                            this.form.curriculum_id = id;
                        }"
                    >
                    </Select2>
                </div>
                <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="course-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="this.globalStore?.closeModal('course-modal')">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="course-save"
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
import Select2 from "../forms/Select2";
import {useGlobalStore} from "../../store/global";


export default {
    components:{
        Select2
    },
    props: {
        show: {
            type: Boolean
        },
        params: {
            type: Object
        },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
    },
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
            url: '/curricula/enrol',
            form: new Form({
                'id': '',
                'curriculum_id': '',
                'enrollment_list': {},
            }),
            search: '',
        }
    },
    methods: {
        submit(method) {
            if (method === 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add(){
            axios.post(this.url, {
                    'enrollment_list' : {
                        0: {
                            'group_id' : this.params.id, // == group_id
                            'curriculum_id': {
                                0 : this.form.curriculum_id
                            }
                        }
                    }
                })
                .then(r => {
                    this.$eventHub.emit('course-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },

    },
    mounted() {
        this.globalStore.registerModal('course-modal');
        this.globalStore.$subscribe((mutation, state) => {
            //console.log(mutation);
            const params = state.modals['course-modal'].params;
            this.form.reset();
            if (typeof (params) !== 'undefined'){
                this.form.populate(params);
                if (this.form.id != ''){
                    this.method = 'patch';
                } else {
                    this.method = 'post';
                }
            }
        });
    },
}
</script>
