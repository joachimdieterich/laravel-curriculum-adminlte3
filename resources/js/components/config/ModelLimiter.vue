<template>
    <div>
        <h5>{{ trans('global.config.model_limiter_title') }}</h5>
        <p>{{ trans('global.config.model_limiter_description') }}</p>

        <h6>{{ trans('global.' + model + '.title') }}</h6>
        <table class="table table-striped">
            <thead>
            <tr class="text-bold">
                <td>{{ trans('global.role.title_singular') }}</td>
                <td>{{ trans('global.config.fields.value') }}</td>
                <td></td>
            </tr>
            </thead>

            <tr v-for="role in roles">
                <td>{{ role.title }}</td>
                <td>
                    <span v-if="!edit || edit_entry != role.id">
                        {{ getValue(role.id) }}
                    </span>
                    <span v-if="edit && edit_entry == role.id">
                        <input :id="key_value+'_role_'+ role.id"
                               :value=" getValue(role.id) "/>
                        <i class="fa fa-save pointer"
                           @click="submit(role.id)"></i>
                    </span>
                </td>
                <td>
                    <span v-if="!edit || edit_entry != role.id"
                          class="pointer"
                          @click="toggleEdit(role.id)">
                        <i class="fa fa-pencil-alt float-right pr-2"></i>
                    </span>
                </td>
            </tr>

        </table>
    </div>
</template>
<script>
import Form from 'form-backend-validation';

export default {
    props: {
        roles: {
            type: Array,
            default: []
        },
        initial_configs: {
            type: Array,
            default: []
        },
        key_value: {
            type: String,
            default: ''
        },
        referenceable_type: {
            type: String,
            default: ''
        },
        model: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            requestUrl: '/configs',
            method: 'post',
            edit: false,
            edit_entry: -1,
            configs: [],
            form: new Form({
                'id': null,
                'key': this.key_value,
                'value': -1,
                'referenceable_type': this.referenceable_type,
                'referenceable_id': '',
                'data_type': 'integer',
            }),
        }
    },
    created() {
    },
    mounted() {
        this.configs = this.initial_configs;
    },
    methods: {
        getValue(role_id) {
            const val = this.configs.filter(
                s => s.key === this.key_value && s.referenceable_type === this.referenceable_type && s.referenceable_id === role_id
            );
            if (typeof (val[0]) !== 'undefined') {
                return val[0].value;
            } else {
                return null;
            }
        },
        filterConfig(role_id) {
            const val = this.configs.filter(
                s => s.key === this.key_value && s.referenceable_type === this.referenceable_type && s.referenceable_id === role_id
            );
            if (typeof (val[0]) !== 'undefined') {
                return val[0];
            } else {
                return -1;
            }
        },
        toggleEdit(role_id) {
            this.edit = !this.edit;
            this.edit_entry = role_id;
        },
        submit(role_id) {
            let currentPath = '';

            if (this.filterConfig(role_id).id > 0) {                            // if config exists
                this.form.id = this.filterConfig(role_id).id;
                currentPath = '/' + this.form.id;
                this.method = 'patch'

                const index = this.configs.findIndex(c => c.id === this.form.id); // remove entry, it will be added again after update
                this.configs.splice(index, 1);
            }

            this.form.value = $('#' + this.key_value + '_role_' + role_id).val();
            this.form.referenceable_id = role_id;
            this.form.submit(this.method, this.requestUrl + currentPath)
                .then(response => this.configs.push(response.config))
                .catch(response => console.log(response));

            this.toggleEdit(role_id);
            this.method = 'post'
        },
    }
}
</script>
