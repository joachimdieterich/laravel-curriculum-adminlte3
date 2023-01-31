<template>
  <div>
      <span v-if="!personal_agenda">
          <MeetingAgendaForm
              v-if="edit"
              :agenda="agenda"
              :meeting_date_id="meeting_date_id"
              @close="toggleEdit()"/>
          <div v-else
               class="px-4">
              <i class="fa fa-pencil-alt text-muted pull-right"
                 @click="toggleEdit()"></i>
              <span v-html="agenda.description"></span>
              <hr>
          </div>
      </span>


      <div class="timeline">

          <AgendaItem
              v-if="personal_agenda"
              v-for="item in items"
              :personal_agenda="personal_agenda"
              :key="'personal_agenda_item_' +  item.id + '_' + uid"
              :item="item"/>
          <AgendaItem
              v-if="!personal_agenda"
              v-for="item in items"
              :key="'agenda_item_' + agenda.id + '_' + item.id  + '_' + uid"
              :agenda_id="agenda.id"
              :item="item"/>

            <div v-if="!personal_agenda">
                <i class="fas fa-plus bg-gray"
                   @click="toggleEdit('newItem')"></i>
                <AgendaItemForm v-if="this.newItem"
                    :agenda_id="agenda.id"
                    @add-agenda-item="toggleEdit('newItem')"/>
            </div>
      </div>
  </div>
</template>
<script>

import MeetingAgendaForm from "./MeetingAgendaForm";
import Form from "form-backend-validation";
import AgendaItemForm from "./AgendaItemForm";
import AgendaItem from "./AgendaItem";

export default {
    name: 'Agenda',
    props: {
        agenda: {
            default: 0
        },
        personal_agenda:  {
            type: Boolean,
            default: false
        },
        meeting_date_id: {
            type: Number,
            default: 0
        }
    },
    data () {
        return {
            uid: '',
            items: {},
            edit: false,
            newItem: false,
            time: null,
            form: new Form({
                'id':'',
                'agenda_id':'',
                'agenda_item_type_id': '',
                'title': '',
                'subtitle': '',
                'description': '',
                'medium_id': '',
                'host_id': '',
                'co_hosts': '',
                'begin': '',
                'end': '',
                'order_id': '',
                'owner_id': '',
            }),
        };
    },
    methods:{
        loadItems() {
            let param = ''
            if (this.personal_agenda === true){
                param = 'meeting_date_id=' + this.meeting_date_id
                // console.log('personalAgenda');
            } else {
                param = 'agenda_id=' + this.agenda.id;
            }
            axios.get('/agendaItemSubscriptions/?' + param)
                .then(response => {
                    this.items = response.data.items;
                })
                .catch(e => {
                    console.log(e.data.errors);
                });
        },
        toggleEdit(field = 'edit') {
            if (field == 'edit') {
                this.edit = !this.edit;
            } else {
                this.newItem = !this.newItem;
            }
            this.loadItems();
        },
    },
    mounted() {
        this.uid = this._uid;
        this.loadItems();
    },
    created() {
        if (this.personal_agenda == true) {
            this.$eventHub.$on('reload_subscribed_agenda', (e) => {
                if (this.meeting_date_id == e.id) {
                    this.loadItems();
                }
            });
        }
        this.$eventHub.$on('reload_agenda', (e) => {
            if (this.agenda.id == e.id) {
                this.loadItems();
            }
        });

    },
    components: {
        AgendaItem,
        AgendaItemForm,
        MeetingAgendaForm,
    }
}
</script>
