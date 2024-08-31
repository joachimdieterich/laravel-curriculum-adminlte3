<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
            <div class="modal-container">
                <div class="card-header">
                    <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.user.create') }}
                    </span>
                        <span v-if="method === 'patch'">
                        {{ trans('global.user.edit') }}
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
                    <div class="form-user "
                         :class="form.errors.username ? 'has-error' : ''"
                    >
                        <label for="username">{{ trans('global.user.fields.username') }} *</label>
                        <input
                            type="text" id="username"
                            name="username"
                            class="form-control"
                            v-model="form.username"
                            :placeholder="trans('global.user.fields.username')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.username" v-text="form.errors.username[0]"></p>
                    </div>
                    <div class="form-user "
                         :class="form.errors.firstname ? 'has-error' : ''"
                    >
                        <label for="firstname">{{ trans('global.user.fields.firstname') }} *</label>
                        <input
                            type="text" id="firstname"
                            name="firstname"
                            class="form-control"
                            v-model="form.firstname"
                            :placeholder="trans('global.user.fields.firstname')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.firstname" v-text="form.errors.firstname[0]"></p>
                    </div>
                    <div class="form-user "
                         :class="form.errors.lastname ? 'has-error' : ''"
                    >
                        <label for="lastname">{{ trans('global.user.fields.lastname') }} *</label>
                        <input
                            type="text" id="lastname"
                            name="lastname"
                            class="form-control"
                            v-model="form.lastname"
                            :placeholder="trans('global.user.fields.lastname')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.lastname" v-text="form.errors.lastname[0]"></p>
                    </div>
                    <div class="form-user "
                         :class="form.errors.email ? 'has-error' : ''"
                    >
                        <label for="email">{{ trans('global.user.fields.email') }} *</label>
                        <input
                            type="text" id="email"
                            name="email"
                            class="form-control"
                            v-model="form.email"
                            :placeholder="trans('global.user.fields.email')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.email" v-text="form.errors.email[0]"></p>
                    </div>
                    <div v-if="method == 'post'"
                        class="form-user "
                         :class="form.errors.password ? 'has-error' : ''"
                    >
                        <label for="email">{{ trans('global.user.fields.password') }} *</label>
                        <input
                            type="text" id="password"
                            name="password"
                            class="form-control"
                            v-model="form.password"
                            :placeholder="trans('global.user.fields.password')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.password" v-text="form.errors.password[0]"></p>
                    </div>

                </div>
                <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="user-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="$emit('close')">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="user-save"
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
    components:{
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
            component_id: this._uid,
            method: 'post',
            url: '/users',
            form: new Form({
                'id':'',
                'username': '',
                'firstname': '',
                'lastname': '',
                'email': '',
                'password': '',
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
                    this.$eventHub.emit('user-added', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        },
        update(){
            axios.patch(this.url + '/' + this.form.id, this.form)
                .then(r => {
                    this.$eventHub.emit('user-updated', r.data);
                })
                .catch(e => {
                    console.log(e.response);
                });
        }
    },
    mounted() {},
}
</script>
