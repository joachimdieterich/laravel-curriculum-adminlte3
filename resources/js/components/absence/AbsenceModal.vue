<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.absences.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.absences.edit') }}
                    </span>
                </h3>
                <div class="card-tools">
                    <button type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body"
                 style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">
                        {{ trans('global.task.fields.title') }} *
                    </label>
                    <input
                        type="text"
                        id="reason"
                        name="reason"
                        class="form-control"
                        v-model="form.reason"
                        placeholder="Fehlgrund"
                        required
                        />
                     <p class="help-block"
                        v-if="form.errors.reason"
                        v-text="form.errors.reason[0]"></p>
                </div>
                <Select2
                    v-if="this.form.absent_user_id == '' "
                    id="user_id"
                    name="user_id"
                    url="/users"
                    model="user"
                    option_id="id"
                    option_label="title"
                    :selected="this.form.absent_user_id"
                    @selectedValue="(id) => {
                        this.form.user_id = id;
                    }"
                >
                </Select2>

                <div class="form-group ">
                    <label for="categorie">
                        {{ trans('global.absences.fields.time') }}
                    </label>
                    <input name="time"
                           type="number"
                           id="time"
                           step="5"
                           v-model="form.time"
                           class="form-control "
                           style="width:100%;"/>
                </div>

                <span class="pull-right custom-control custom-switch custom-switch-on-green">
                    <input
                        v-model="form.done"
                        type="checkbox"
                        class="custom-control-input pt-1 "
                        :id="'done_input' + component_id">
                      <label class="custom-control-label font-weight-light"
                         :for="'done_input' + component_id">
                          {{ trans('global.absences.fields.done') }}
                      </label>
                </span>
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="task-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="task-save"
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
    import Editor from '@tinymce/tinymce-vue';
    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css';
    import {useGlobalStore} from "../../store/global";
    import Select2 from "../forms/Select2.vue";

    export default {
        name: 'absence-modal',
        components:{
            Select2,
            Editor,
            VueDatePicker
        },
        props: { },
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
                url: '/absences',
                form: new Form({
                    'id':'',
                    'reason': '',
                    'absent_user_id': '',
                    'referenceable_type': null,
                    'referenceable_id': null,
                    'done': false,
                    'time': 0
                }),
            }
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
                        this.$eventHub.emit('absence-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('absence-updated', r.data);
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
                        if (this.form.id !== ''){
                            this.method = 'patch';
                        } else {
                            this.method = 'post';
                        }
                    }
                }
            });

            this.form.start_date = new Date();
        },
    }
</script>

