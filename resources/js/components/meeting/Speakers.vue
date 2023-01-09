<template>
  <div>
      <div
          v-for="speaker in this.speakers"
          class="pull-left"
          style="min-width: 300px;">
          <avatar :key="'agenda_item_'+ item.id + '_speaker_id_' + speaker.id"
                    css="m-2"

                    data-toggle="tooltip"
                    label=true
                    :title="speaker.user.firstname + ' ' + speaker.user.lastname"
                    :subtitle="speaker.title"
                    :firstname="speaker.user.firstname"
                    :lastname="speaker.user.lastname"
                    medium_id="null"
                    size="40"
          ></avatar>
          <i v-if="edit"
             class="fa fa-trash text-danger"
             @click="remove(speaker.id)"></i>
      </div>

    <span
        style="min-width: 300px;"
        class="pointer"
        v-if="showAddBtn"
        @click="toggleEdit()">
      <i class="fas fa-plus m-3 text-muted"
         v-if="!edit"></i> {{ trans('global.meeting.fields.speakers') }}
    </span>

    <div v-if="edit"
         class="p-2">
        <div class="form-group pt-2">
            <Select2
                :id="'userlist_agenda_item_'+ item.id"
                model="user"
                :selected="this.form.user_id"
                url="/users"
                :placeholder="trans('global.pleaseSelect')"
                @selectedValue="(id) => this.form.user_id = id"
            ></Select2>
        </div>
        <div class="form-group pt-2">
            <label for="title">{{ trans('global.agendaItem.fields.title') }}</label>
            <input
                type="text" id="title"
                name="title"
                class="form-control"
                v-model="form.title"
                :placeholder="trans('global.agendaItem.fields.title')"
            />
        </div>
        <div class="form-group">
            <button
                type="button"
                class="btn btn-info"
                data-widget="remove"
                @click="toggleEdit()">
                {{ trans('global.cancel') }}
            </button>
            <button
                class="btn btn-info"
                @click="submit()">
                {{ trans('global.save') }}
            </button>
        </div>
    </div>

  </div>
</template>
<script>
import Avatar from "../uiElements/Avatar"
import Form from "form-backend-validation";
import Select2 from "../forms/Select2";

export default {
  name: 'Speakers',
  components: {Avatar, Select2},
  props: {
    item: {},
    showAddBtn: {}
  },
    data () {
        return {
            speakers: {},
            edit: false,
            form: new Form({
                'id':'',
                'agenda_item_id': this.item.id,
                'title':'',
                'user_id': '',
            }),

        };
    },
    methods: {
        toggleEdit() {
            this.edit = !this.edit;
        },
        async submit() {
            try {
                this.speakers = (await axios.post('/agendaItemSpeakers', this.form)).data.speakers;
            } catch (error) {
                console.log(error);
            }
        },
        async remove(id) {
            try {
                this.speakers = (await axios.delete('/agendaItemSpeakers/' + id)).data.speakers;
            } catch (error) {
                console.log(error);
            }
        },
    },
    mounted() {
        this.speakers = this.item.speakers;
    }

}
</script>
