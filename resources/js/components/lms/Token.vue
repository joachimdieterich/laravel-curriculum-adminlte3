<template>
    <div class="col-12 px-0">
        Kein Nutzer-Token (Sicherheitsschlüssel) vorhanden/Token zurücksetzen.<br> Bitte Token für die aktuelle
        Organization eingeben.
        (<a href="https://docs.moodle.org/39/de/Sicherheitsschl%C3%BCssel"
            target="_blank">?</a>)
        <div class="form-group "
             :class="form.errors.token ? 'has-error' : ''"
        >
            <input
                type="text" id="token"
                name="token"
                class="form-control"
                v-model="form.token"
                placeholder="token"
                required
            />
            <p class="help-block" v-if="form.errors.token" v-text="form.errors.token[0]"></p>
        </div>

        <span class="pull-right">
                 <button class="btn btn-primary" @click="submit()">{{ trans('global.save') }}</button>
            </span>


    </div>
</template>

<script>
import Form from 'form-backend-validation';

export default {
    data() {
        return {
            method: 'post',
            form: new Form({
                'token': false,
            }),

            errors: {}
        }
    },
    methods: {

        async submit() {
            try {
                const token = (await axios.post('/lmsUserTokens',
                    {
                        'token': this.form.token,
                    })).data.token;
                this.$emit('newToken', token)
            } catch (e) {
                console.log(e);
            }
        },

    },

}
</script>
