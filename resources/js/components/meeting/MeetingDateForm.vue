<template>
  <form>
        <div class="card-body">
              <div class="form-meeting-date"
                   :class="errors.uid ? 'has-error' : ''"
              >
                    <label for="uid">{{ trans('global.meetingDate.fields.uid') }}</label>
                    <input
                        type="text" id="uid"
                        name="uid"
                        class="form-control"
                        v-model="form.uid"
                        placeholder="uid"
                    />
                    <p class="help-block" v-if="errors.uid" v-text="errors.uid[0]"></p>
              </div>
            <div class="form-meeting-date"
                 :class="errors.access_token ? 'has-error' : ''"
            >
                <label for="access_token">{{ trans('global.meetingDate.fields.access_token') }}</label>
                <input
                    type="text" id="access_token"
                    name="access_token"
                    class="form-control"
                    v-model="form.access_token"
                    placeholder="access_token"
                />
                <p class="help-block" v-if="errors.access_token" v-text="errors.access_token[0]"></p>
            </div>
            <div class="form-meeting-date"
                 :class="errors.title ? 'has-error' : ''"
            >
                <label for="title">{{ trans('global.meetingDate.fields.title') }} *</label>
                <input
                    type="text" id="title"
                    name="title"
                    class="form-control"
                    v-model="form.title"
                    :placeholder=" trans('global.meetingDate.fields.title')"
                    required
                />
                <p class="help-block" v-if="errors.title" v-text="errors.title[0]"></p>
            </div>
            <div class="form-group mb-2">
                 <label for="description">{{ trans('global.meetingDate.fields.description') }}</label>
                 <textarea
                     id="description"
                     name="description"
                     :placeholder=" trans('global.meetingDate.fields.description')"
                     class="form-control description my-editor "
                     v-model.trim="form.description"
                 ></textarea>
                <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
            </div>
            <div class="form-group mb-2">
                <label for="address">{{ trans('global.meetingDate.fields.address') }}</label>
                 <textarea
                     id="address"
                     name="address"
                     :placeholder=" trans('global.meetingDate.fields.address')"
                     class="form-control address my-editor "
                     v-model.trim="form.address"
                 ></textarea>
                <p class="help-block" v-if="form.errors.address" v-text="form.errors.address[0]"></p>
            </div>
            <label for="date_picker">{{ trans('global.meetingDate.title_singular') }} *</label>
            <date-picker
                :id="'date_picker_meeting_id'+meeting.id"
                class="w-100"
                 v-model="time"
                 type="datetime" range
                 valueType="YYYY-MM-DD HH:mm:ss"></date-picker>
            <div class="form-group pt-2">
                <label for="type">{{ trans('global.meetingDate.fields.type') }} *</label>
                <input
                    type="text" id="type"
                    name="type"
                    class="form-control"
                    v-model="form.type"
                    :placeholder=" trans('global.meetingDate.fields.type')"
                    required
                />
            </div>
        </div>
        <div class="card-footer">
              <div class="form-group m-2">
                    <button type="button" class="btn btn-info" data-widget="remove" @click="$emit('close')">
                        {{ trans('global.cancel') }}
                    </button>
                    <button class="btn btn-info" @click="submit()">{{ trans('global.save') }}</button>
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
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    name: 'MeetingDateForm',
    props: {
        'meeting': Object,
    },
    data() {
        return {
            method: 'post',
            requestUrl: '/meetingDates',
            time: null,
            form: new Form({
                'id':'',
                'uid':'',
                'meeting_id': this.meeting.id,
                'access_token': '',
                'title': '',
                'description': '',
                'address': '',
                'begin': '',
                'end': '',
                'owner_id': '',
                'type': '',
            }),
            errors: {}
        };
    },
    methods: {
        setData(data) {
            if (data == 'reset') {
                this.form.reset();
                this.method = 'post';
            } else {
                this.form.populate(data);
                this.method = 'patch';
            }

            this.time = [moment().format("YYYY-MM-DD HH:mm:ss"), moment().add(30, 'minutes').format("YYYY-MM-DD HH:mm:ss")];
            this.$initTinyMCE();
        },
        async submit() {
            try {
                this.form.description = tinyMCE.get('description').getContent();
                this.form.address = tinyMCE.get('address').getContent();
                this.form.begin = this.time[0];
                this.form.end = this.time[1];
                if (this.method === 'patch') {
                    this.new_entry = (await axios.patch('/meetingDates/' + this.form.id, this.form)).data.meetingDate;
                } else {
                    this.new_entry = (await axios.post('/meetingDates', this.form)).data.meetingDate;
                }
                this.close();
            } catch (error) {
                this.form.errors = error.response.data.form.errors;
            }
        },
        async destroy() {
            this.error = (await axios.delete('/meetingDates/' + this.form.id)).data;
        },
    },
    mounted() {


    },
    components: {
        DatePicker
    },
}
</script>
