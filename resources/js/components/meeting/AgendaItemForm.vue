<template>
    <div class="timeline-item">
        <div class="timeline-body">
            <div class="form-group "
                 :class="form.errors.agenda_item_type_id ? 'has-error' : ''">
                <Select2
                    :id="'agenda_'+ agenda_id +'_item_type_id'"
                    model="agendaItemType"
                    :selected="this.form.agenda_item_type_id"
                    url="/agendaItemTypes"
                    :placeholder="trans('global.pleaseSelect')"
                    @selectedValue="(id) => this.form.agenda_item_type_id = id"
                ></Select2>
            </div>
            <div :class="errors.title ? 'has-error' : ''">
                <label for="title">{{ trans('global.agendaItem.fields.title') }} *</label>
                <input
                    type="text" id="title"
                    name="title"
                    class="form-control"
                    v-model="form.title"
                    :placeholder=" trans('global.agendaItem.fields.title')"
                    required
                />
                <p class="help-block" v-if="errors.title" v-text="errors.title[0]"></p>
            </div>
            <div :class="errors.subtitle ? 'has-error' : ''">
                <label for="subtitle">{{ trans('global.agendaItem.fields.subtitle') }}</label>
                <input
                    type="text" id="subtitle"
                    name="subtitle"
                    class="form-control"
                    v-model="form.subtitle"
                    :placeholder=" trans('global.agendaItem.fields.subtitle')"
                />
                <p class="help-block" v-if="errors.subtitle" v-text="errors.subtitle[0]"></p>
            </div>
            <div class="form-group mb-2">
                <label for="description">{{ trans('global.agendaItem.fields.description') }}</label>
                <textarea
                    :id="'agenda_'+ this.agenda_id +'_description'"
                    :name="'agenda_'+ this.agenda_id +'_description'"
                    :placeholder=" trans('global.agendaItem.fields.description')"
                    class="form-control description my-editor "
                    v-model.trim="form.description"
                ></textarea>
                <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
            </div>
            <div class="form-group mb-2">
                <label for="date_picker">{{ trans('global.selectDateRange') }}</label>
                <date-picker
                    :id="'date_picker_meeting_id'+agenda_id"
                    class="w-100"
                    v-model="time"
                    type="datetime" range
                    valueType="YYYY-MM-DD HH:mm:ss"></date-picker>
            </div>
            <div class="form-group mb-2">
                <button
                    v-if="this.form.id != ''"
                    type="button"
                    class="btn btn-info py-2"
                    @click="addMedia()">
                    {{ trans('global.media.add') }}
                </button>
            </div>
        </div>

        <div class="timeline-footer">
            <button
                type="button"
                class="btn btn-info"
                data-widget="remove"
                @click="$emit('toggleEdit')">
                {{ trans('global.cancel') }}
            </button>
            <button
                class="btn btn-info"
                @click="submit">
                {{ trans('global.save') }}
            </button>
        </div>
    </div>
</template>
<script>
import Form from "form-backend-validation";
import Select2 from "../forms/Select2";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    name: 'AgendaItemForm',
    props: {
        'agenda_id': Number,
        item: {
            type: Object,
        }
    },
    data() {
        return {
            method: 'post',
            requestUrl: '/agendaItems',
            time: null,
            form: new Form({
                'id': '',
                'agenda_id':this.agenda_id,
                'agenda_item_type_id':'',
                'title': '',
                'subtitle': '',
                'description': '',
                'medium_id': null,
                'host_id': null,
                'co_hosts': null,
                'begin': '',
                'end': '',
                'order_id': 0,
                'owner_id': null,
            }),
            agendaItem: {},
            types: [],
            errors: {},
            editor: null
        };
    },
    methods: {
      async submit() {
          try {
              this.form.description = tinyMCE.get('agenda_'+ this.agenda_id +'_description').getContent();

              this.form.begin = this.time[0];
              this.form.end   = this.time[1];
              if (typeof this.form.id !== '') {
                  this.agendaItem = (await axios.patch('/agendaItems/' + this.form.id, this.form)).data.agendaItem;
              } else {
                  this.agendaItem = (await axios.post('/agendaItems', this.form)).data.agendaItem;
              }
              this.$emit('add-agenda-item');
          } catch (error) {
              console.log(error);
          }
      },
        addMedia() {
            this.$modal.show(
                'medium-create-modal',
                {
                    'eventHubCallbackFunction': 'add_media_to_agenda_item',
                    'eventHubCallbackFunctionParams': this.form.id
                });
        },
        /*setSelectedValue(id){
          console.log(id);
            this.form.agenda_item_type_id = id;
        }*/
    },
    mounted() {
        if (typeof this.item != 'undefined'){
            this.form.populate(this.item);
        }
        axios.get('/agendaItemTypes')
            .then(response => {
                this.types = response.data.dates;
            })
            .catch(e => {
                this.errors = e.data.errors;
            });
        this.time = [moment().format("YYYY-MM-DD HH:mm:ss"), moment().add(30, 'minutes').format("YYYY-MM-DD HH:mm:ss")];
        this.$initTinyMCE();
    },
    created() {
        this.$eventHub.$on('add_media_to_agenda_item', (e) => {
            if (this.form.id == e.id) {
                this.form.medium_id = e.selectedMediumId;
            }
        });
    },
    components: {
        DatePicker,
        Select2
    },
}
</script>
