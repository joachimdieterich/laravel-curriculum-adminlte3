<template>
    <div>
        <Select2
            id="token-organization"
            url="/organizations"
            css="mb-3"
            model="organization"
            @selectedValue="id => form.organization_id = id[0]"
        />

        {{ trans('global.lms.token_helper') }}
        (<a href="https://docs.moodle.org/39/de/Sicherheitsschl%C3%BCssel" target="_blank">?</a>)
        <input
            id="token"
            type="text"
            class="form-control mb-3"
            v-model.trim="form.token"
            placeholder="Token"
            required
        />

        <span class="d-flex justify-content-end">
            <button
                type="button"
                class="btn btn-primary"
                :disabled="!form.organization_id || !form.token"
                @click="submit()"
            >
                {{ trans('global.save') }}
            </button>
        </span>
    </div>
</template>
<script>
import Form from 'form-backend-validation';
import Select2 from "../forms/Select2.vue";

export default {
    components: { Select2 },
    data() {
        return {
            form: new Form({
                token: '',
                organization_id: null,
            }),
            organizations: [],
        }
    },
    methods: {
        submit() {
            axios.post('/lmsUserTokens', this.form)
                .then(response => this.$emit('newToken', response.data))
                .catch(error => console.log(error));
        },
    },
}
</script>