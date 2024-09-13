<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.group.create') }}
                    </span>
                        <span v-if="method === 'patch'">
                        {{ trans('global.group.edit') }}
                    </span>
                    </h3>
                    <div class="card-tools">
                        <button type="button"
                                class="btn btn-tool"
                                @click="$emit('close')">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="form-group "
                         :class="form.errors.title ? 'has-error' : ''"
                    >
                        <label for="title">{{ trans('global.group.fields.title') }} *</label>
                        <input
                            type="text" id="title"
                            name="title"
                            class="form-control"
                            v-model="form.title"
                            placeholder="Title"
                            required
                        />
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>

                    <Select2
                        id="grade_id"
                        name="grade_id"
                        url="/grades"
                        model="grade"
                        option_id="id"
                        option_label="title"
                        :selected="this.form.grade_id"
                        @selectedValue="(id) => {
                            this.form.grade_id = id;
                        }"
                    >
                    </Select2>

                    <Select2
                        id="period_id"
                        name="period_id"
                        url="/periods"
                        model="period"
                        option_id="id"
                        option_label="title"
                        :selected="this.form.period_id"
                        @selectedValue="(id) => {
                            this.form.period_id = id;
                        }"
                    >
                    </Select2>

                    <Select2
                        id="organization_id"
                        name="organization_id"
                        url="/organizations"
                        model="organization"
                        option_id="id"
                        option_label="title"
                        :selected="this.form.organization_id"
                        @selectedValue="(id) => {
                            this.form.organization_id = id;
                        }"
                    >
                    </Select2>
                </div>
                <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="group-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="$emit('close')">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="group-save"
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
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            url: '/groups',
            form: new Form({
                'id':'',
                'title': '',
                'grade_id': '',
                'period_id': '',
                'organization_id': '',
            }),
            search: '',
        }
    },
    watch: {
        params: function(newVal, oldVal) {
            if (typeof (newVal.id) == 'undefined'){
                this.form.reset();
            }
            this.form.populate(newVal);

            if (this.form.id != ''){
                this.method = 'patch';
            }
        },
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
            axios.post(this.url, this.form)
                .then(r => {
                    this.$eventHub.emit('group-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update(){
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('group-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        }
    },
    mounted() {},
}
</script>
