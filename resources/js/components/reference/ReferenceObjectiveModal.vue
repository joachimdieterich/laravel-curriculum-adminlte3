<template>
    <Transition name="modal">
        <div v-if="this.globalStore.modals['reference-objective-modal']?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.referenceable_types.objective') }}
                </h3>
                <div class="card-tools">
                    <button v-permission="'objective_delete'"
                            v-if="method !== 'post'"
                            type="button"
                            class="btn btn-tool"
                            @click="del()">
                        <i class="fa fa-trash text-danger"></i>
                    </button>
                    <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button"
                            class="btn btn-tool"
                            @click="this.globalStore?.closeModal('reference-objective-modal')">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div v-if="method === 'post'" class="form-group ">
                    <Select2
                        id="curriculum_id"
                        name="curriculum_id"
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
                    <div v-if="this.form.curriculum_id"
                     class="form-group ">
                    <Select2
                        id="terminalObjectives_id"
                        name="terminalObjectives_id"
                        :url="'/curricula/' + this.form.curriculum_id + '/terminalObjectives'"
                        model="terminalObjective"
                        option_id="id"
                        option_label="title"
                        :selected="null"
                        @selectedValue="(id) => {
                            this.form.terminal_objective_id = id;
                        }"
                    >
                    </Select2>
                </div>
                <div v-if="this.form.terminal_objective_id"
                class="form-group ">
                    <Select2
                        id="enablingObjectives_id"
                        name="enablingObjectives_id"
                        :url="'/terminalObjectives/' + this.form.terminal_objective_id + '/enablingObjectives'"
                        model="enablingObjective"
                        option_id="id"
                        option_label="title"
                        selected="null"
                        @selectedValue="(id) => {
                            this.form.enabling_objective_id = id;
                        }"
                    >
                    </Select2>
                </div>

                <div class="form-group ">
                    <label for="description">
                        {{ trans('global.description') }}
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control description my-editor "
                        v-model="form.description"
                    ></textarea>
                </div>
            </div>

                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="grade-cancel"
                             type="button"
                             class="btn btn-default"
                             @click="this.globalStore?.closeModal('reference-objective-modal')">
                             {{ trans('global.cancel') }}
                         </button>
                         <button
                             id="grade-save"
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
            Select2,
        },
        props: {
            params: {
                type: Object
            },  //{ 'modelId': curriculum.id, 'modelUrl': 'curriculum' , 'shareWithToken': true, 'canEditCheckbox': false}
        },
        setup () { //use database store
            const globalStore = useGlobalStore();
            return {
                globalStore
            }
        },
        data() {
            return {
                component_id: this._uid,
                method: 'post',
                url: '',
                form: new Form({
                    'id': null,
                    'subscribable_type': null,
                    'subscribable_id': null,
                    'curriculum_id': null,
                    'terminal_objective_id': null,
                    'enabling_objective_id': null,
                    'description': null
                }),
            }
        },
        watch: {
            params: function(newVal, oldVal) {
                this.form.reset();
                this.form.populate(newVal);
                this.url = newVal.url;

                if (this.form.id != null){
                    this.method = 'patch';
                } else {
                    this.method = 'post';
                }
            },

        },
        methods: {
             submit(method) {
                 if (method == 'patch') {
                     this.update();
                 } else {
                     this.add();
                 }
            },
            add(){
                axios.post(this.url, this.form)
                    .then(r => {
                        this.$eventHub.emit('reference-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('reference-updated', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            }
        },
        mounted() {
            this.globalStore.registerModal('reference-objective-modal');
        },
    }
</script>

