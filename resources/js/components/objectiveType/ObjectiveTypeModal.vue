<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.objectiveType.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.objectiveType.edit') }}
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
            <form >
                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="form-group "
                        :class="form.errors.title ? 'has-error' : ''"
                          >
                        <label for="title">
                            {{ trans('global.objectiveType.fields.title') }} *
                        </label>
                        <input
                            type="text" id="title"
                            name="title"
                            class="form-control"
                            v-model="form.title"
                            placeholder="Title"
                            required
                            />
                         <p class="help-block"
                            v-if="form.errors.title"
                            v-text="form.errors.title[0]"></p>
                    </div>
                </div>
                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="objective-cancel"
                             type="button"
                             class="btn btn-default"
                             @click="globalStore?.closeModal($options.name)">
                             {{ trans('global.cancel') }}
                         </button>
                         <button
                             id="objective-save"
                             class="btn btn-primary"
                             @click="submit(method)" >
                             {{ trans('global.save') }}
                         </button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    </Transition>
</template>
<script>
import Form from 'form-backend-validation';
import Editor from '@tinymce/tinymce-vue';
import {useGlobalStore} from "../../store/global";

export default {
    name: 'objective-type-modal',
    components: {
        Editor
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
            url: '/objectiveTypes',
            form: new Form({
                'id':'',
                'title': '',
                'external_id': '',
                'country_id': 'DE',
                'state_id': 'DE-RP',
            }),
            countries: [],
            states: [],
            search: '',
        }
    },
    methods: {
        async submit(method) {
            try {
                if (method === 'patch') {
                    this.location = (await axios.patch(this.url + '/' + this.form.id, this.form)).data.message;
                } else {
                    this.location = (await axios.post(this.url, this.form)).data.message;
                }
            } catch (error) {
                this.form.errors = error.response.data.form.errors;
            }
        },
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
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
