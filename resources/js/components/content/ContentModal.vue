<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
            class="modal-mask"
            @click.self="globalStore.closeModal($options.name)"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <span v-if="method === 'post'">
                            {{ trans('global.content.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.content.edit') }}
                        </span>
                    </h3>
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="form-group"
                                :class="form.errors.title ? 'has-error' : ''"
                            >
                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    v-model.trim="form.title"
                                    :placeholder="trans('global.title') + ' *'"
                                    required
                                />
                                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                            </div>
        
                            <Editor
                                id="content"
                                name="content"
                                :placeholder="trans('global.content.fields.content')"
                                class="form-control"
                                :init="tinyMCE"
                                v-model="form.content"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="pull-right">
                        <button
                            id="content-cancel"
                            type="button"
                            class="btn btn-default"
                            @click="globalStore?.closeModal($options.name)"
                        >
                            {{ trans('global.cancel') }}
                        </button>
                        <button
                            id="content-save"
                            class="btn btn-primary ml-3"
                            :disabled="!form.title"
                            @click="submit()"
                        >
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
import {useGlobalStore} from "../../store/global";

export default {
    name: 'content-modal',
    components: {
        Editor,
    },
    props: {},
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            form: new Form({
                id: '',
                title: '',
                content: '',
                subscribable_id: null,
                subscribable_type: null,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia table lists code autoresize"
                ],
                {
                    callback: 'insertContent',
                    callbackId: this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | curriculummedia link mathjax code",
                ""
            ),
        }
    },
    methods: {
        submit() {
            if (this.method == 'patch') {
                this.update();
            } else {
                this.add();
            }

            this.globalStore.closeModal(this.$options.name);
        },
        add() {
            axios.post('/contents', this.form)
                .then(r => {
                    this.$eventHub.emit('content-added', r.data);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        update() {
            axios.patch('/contents/' + this.form.id, this.form)
                .then(r => {
                    r.data.subscribable_id = this.form.subscribable_id;
                    this.$eventHub.emit('content-updated', r.data);
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show && !state.modals[this.$options.name].lock) {
                this.globalStore.lockModal(this.$options.name);
                const params = state.modals[this.$options.name].params;

                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.subscribable_type = params.subscribable_type;
                    this.form.subscribable_id = params.subscribable_id;
                    this.form.populate(params);
                    if (this.form.id !== '') {
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