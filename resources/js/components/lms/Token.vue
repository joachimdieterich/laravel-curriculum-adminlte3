<template>
    <div class="col-12 px-0">
        Kein Nutzer-Token (Sicherheitsschlüssel) vorhanden/Token zurücksetzen.
        <br>
        Bitte Token für die aktuelle Organization eingeben.
        (<a href="https://docs.moodle.org/39/de/Sicherheitsschl%C3%BCssel" target="_blank">?</a>)
        <div class="form-group">
            <input
                id="token"
                name="token"
                type="text"
                class="form-control"
                v-model="form.token"
                placeholder="Token"
                required
            />
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
            form: new Form({
                token: '',
            }),
        }
    },
    methods: {
        submit() {
            axios.post('/lmsUserTokens', { token: this.form.token })
                .then(response => this.$emit('newToken'))
                .catch(error => console.log(error));
        },
    },
}
</script>