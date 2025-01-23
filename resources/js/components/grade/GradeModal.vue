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
                            {{ trans('global.grade.create') }}
                        </span>
                        <span v-if="method === 'patch'">
                            {{ trans('global.grade.edit') }}
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
                    <div class="form-group "
                        :class="form.errors.title ? 'has-error' : ''"
                          >
                        <label for="title">
                            {{ trans('global.grade.fields.title') }} *
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

                    <div class="form-group "
                         :class="form.errors.external_begin ? 'has-error' : ''"
                    >
                        <label for="external_begin">
                            {{ trans('global.grade.fields.external_begin') }} *
                        </label>
                        <input
                            type="text" id="external_begin"
                            name="external_begin"
                            class="form-control"
                            v-model="form.external_begin"
                            placeholder="external_begin"
                            required
                        />
                        <p class="help-block"
                           v-if="form.errors.external_begin"
                           v-text="form.errors.external_begin[0]"></p>
                    </div>

                    <div class="form-group "
                         :class="form.errors.external_end ? 'has-error' : ''"
                    >
                        <label for="external_end">
                            {{ trans('global.grade.fields.external_end') }} *
                        </label>
                        <input
                            type="text" id="external_end"
                            name="external_end"
                            class="form-control"
                            v-model="form.external_end"
                            placeholder="external_end"
                            required
                        />
                        <p class="help-block"
                           v-if="form.errors.external_end"
                           v-text="form.errors.external_end[0]"></p>
                    </div>

                    <Select2
                        id="organization_type_id"
                        name="organization_type_id"
                        url="/organizationTypes"
                        model="organizationType"
                        option_id="id"
                        option_label="title"
                        :selected="this.form.organization_type_id"
                        @selectedValue="(id) => {
                        this.form.organization_type_id = id;
                    }"
                    >
                    </Select2>
                </div>

                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="grade-cancel"
                             type="button"
                             class="btn btn-default"
                             @click="globalStore?.closeModal($options.name)">
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
import {useGlobalStore} from "../../store/global";
import Select2 from "../forms/Select2.vue";

export default {
    name: 'grade-modal',
    components:{
        Select2,
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
            url: '/grades',
            form: new Form({
                'id':'',
                'title': '',
                'external_begin': null,
                'external_end': null,
                'organization_type_id': 1
            }),
            countries: [],
            states: [],
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink link curriculummedia autoresize"
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                }
            ),
            search: '',
        }
    },
    methods: {
        submit(method) {
            this.form.begin = this.form.date[0];
            this.form.end = this.form.date[1];

            if (method == 'patch') {
                this.update();
            } else {
                this.add();
            }
        },
        add() {
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('grade-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update() {
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('grade-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        }
    },
    mounted() {
        this.globalStore.registerModal(this.$options.name);
        this.globalStore.$subscribe((mutation, state) => {
            if (state.modals[this.$options.name].show) {
                const params = state.modals[this.$options.name].params;
                this.form.reset();
                if (typeof (params) !== 'undefined') {
                    this.form.populate(params);
                    this.form.date = [this.form.begin, this.form.end];
                    if (this.form.id != '') {
                        this.method = 'patch';
                    } else {
                        this.method = 'post';
                    }
                }
            }
        });

        const startDate = new Date();
        const endDate = new Date(new Date().setDate(startDate.getDate() + 7));
        this.form.date = [startDate, endDate];
    },
}
</script>

