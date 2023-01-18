<template>
  <form>
        <div class="card-body">
            <div :class="errors.title ? 'has-error' : ''"
            >
                <label for="title">{{ trans('global.agenda.fields.title') }} *</label>
                <input
                    type="text" id="title"
                    name="title"
                    class="form-control"
                    v-model="form.title"
                    :placeholder=" trans('global.agenda.fields.title')"
                    required
                />
                <p class="help-block" v-if="errors.title" v-text="errors.title[0]"></p>
            </div>
            <div class="form-group mb-2">
                 <label for="description">{{ trans('global.agenda.fields.description') }}</label>
                 <textarea
                     :id="'description_agenda_meeting_date_id_' + meeting_date_id"
                     :name="'description_agenda_meeting_date_id_' + meeting_date_id"
                     :placeholder="trans('global.agenda.fields.description')"
                     class="form-control description my-editor "
                     v-model.trim="form.description"
                 ></textarea>
                <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
            </div>
        </div>
        <div class="card-footer">
              <div class="form-group m-2">
                    <button type="button" class="btn btn-info" data-widget="remove" @click="$emit('close')">
                        {{ trans('global.cancel') }}
                    </button>
                    <button
                        class="btn btn-info"
                        @click="submit()">
                        {{ trans('global.save') }}
                    </button>
                    <button
                        v-if="form.id != ''"
                        class="btn btn-danger pull-right"
                        @click="destroy()">
                      <i class="fa fa-trash"></i>{{ trans('global.delete') }}
                    </button>
              </div>
        </div>
  </form>
</template>
<script>
import Form from 'form-backend-validation';

export default {
    name: 'MeetingAgendaForm',
    props: {
        'agenda': Object,
        'meeting_date_id': Number,
    },
    data() {
        return {
            method: 'post',
            requestUrl: '/agendas',
            form: new Form({
                'id':'',
                'meeting_date_id': this.meeting_date_id,
                'title': '',
                'description': '',
                'owner_id': '',
            }),
            errors: {},
            editor: null
        };
    },
    methods: {
        async submit() {
            try {
                this.form.description = tinyMCE.get('description_agenda_meeting_date_id_' + this.meeting_date_id).getContent();

                if (this.method === 'patch') {
                    this.new_entry = (await axios.patch('/agendas/' + this.form.id, this.form)).data.agenda;
                } else {
                    this.new_entry = (await axios.post('/agendas', this.form)).data.agenda;
                }
                this.$emit('close');
            } catch (error) {
                this.form.errors = error.response.data.form.errors;
            }
        },
        async destroy() {
            this.error = (await axios.delete(this.requestUrl + '/' + this.form.id)).data;
        },

    },
    mounted() {
        if (typeof this.agenda != 'undefined') {
            this.form.populate(this.agenda);
            this.method = 'patch';
        }
        this.$initTinyMCE();
    },

}
</script>
