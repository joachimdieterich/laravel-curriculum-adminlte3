<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask">
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.kanban.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.kanban.edit') }}
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

                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="form-group">
                        <input
                            type="text"
                            id="version"
                            name="version"
                            class="form-control"
                            v-model.trim="form.version"
                            :placeholder="trans('global.metadataset.fields.version')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.version" v-text="form.errors.version[0]"></p>
                    </div>
                </div>
                <div class="card-footer">
                         <span class="pull-right">
                             <button
                                 id="kanban-cancel"
                                 type="button"
                                 class="btn btn-default"
                                 @click="globalStore?.closeModal($options.name)">
                                 {{ trans('global.cancel') }}
                             </button>
                             <button
                                 id="kanban-save"
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
    import axios from "axios";
    import {useGlobalStore} from "../../store/global";

    export default {
        name: 'metadataset-modal',
        components:{},
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
                url: '/metadatasets',
                form: new Form({
                    'id': '',
                    'version':  '',
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
                        this.$eventHub.emit('metadataset-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                console.log('update');
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('metadataset-updated', r.data);
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
        },
    }
</script>

