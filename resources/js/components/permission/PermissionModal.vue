<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.permission.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.permission.edit') }}
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
                        <label for="title">{{ trans('global.permission.fields.title') }} *</label>
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
                </div>

                <div class="card-footer">
                     <span class="pull-right">
                         <button
                             id="permission-cancel"
                             type="button"
                             class="btn btn-default"
                             @click="$emit('close')">
                             {{ trans('global.cancel') }}
                         </button>
                         <button
                             id="permission-save"
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

    export default {
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
                component_id: this._uid,
                method: 'post',
                url: '/permissions',
                form: new Form({
                    'id':'',
                    'title': '',
                }),
                search: '',
            }
        },
        watch: {
            params: function(newVal, oldVal) {
                this.form.reset();
                this.form.populate(newVal);
                if (this.form.id != ''){
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
                        this.$eventHub.emit('permission-added', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            },
            update() {
                console.log('update');
                axios.patch(this.url + '/' + this.form.id, this.form)
                    .then(r => {
                        this.$eventHub.emit('permission-updated', r.data);
                    })
                    .catch(e => {
                        console.log(e.response);
                    });
            }
        },
        mounted() {
        },
    }
</script>

