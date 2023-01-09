<template>
    <div>
        <i :class="item.type.icon_css + ' ' + item.type.color"></i>

          <div class="timeline-item">
              <span class="time">
                    <i class="fas fa-clock"></i>
                    {{ postDate(item.begin, item.end) }}
              </span>
              <h3 class="timeline-header text-bold">
                    <span class="pointer">
                        <i v-if="item.subscriptions.length > 0 "
                           class="fas fa-star text-primary"
                           @click="unsubscribe(item)"></i>
                         <i v-else
                            class="far fa-star text-gray"
                            @click="subscribe(item)"></i>
                    </span>

                  <a>
                      {{ item.title }}
                      <i v-if="!personal_agenda"
                         class="fa fa-pencil-alt text-muted"
                         @click="toggleEdit('editItem')"></i>
                  </a>

                  <i v-if="!personal_agenda"
                     class="pull-right fa fa-trash text-danger"
                     @click="destroy(item.id)"></i>

                  <p class="pt-2 pl-4 mb-0 small">
                      {{ item.subtitle }}
                  </p>

              </h3>
              <Speakers
                      :item="item"
                      :show-add-btn="showAddBtn"/>
              <hr v-if="item.speakers.length > 0"
                      class="my-0 clearfix">
              <AgendaItemForm
                    v-if="edit"
                    class="p-2"
                    :agenda_id="item.agenda_id"
                    :item="item"
                    @add-agenda-item="toggleEdit()"
                />
              <span v-else>
                  <div class="p-2">
                      <img v-if="item.medium_id != null"
                          class="border pull-right mb-2"
                          :src="'/media/'+item.medium_id"
                           width="200" >
                      <span v-html="item.description"></span>
                  </div>
                  <hr class="my-0 clearfix">

                  <AgendaItemMedia
                      :media="item.media"
                      :subscription="item"
                      :showAddBtn="showAddBtn"
                  />

              </span>
              <div v-if="!edit"
                   class="timeline-footer">

                  <VideoConference
                      v-if="item.videoconferences.length == 0"
                      modus="add"
                      :meetingID="'agenda_item'+item.id"
                      :meetingName="item.title"
                      :endCallbackUrl="'/agendaItems/'+item.id"
                      subscribable_type="App\AgendaItem"
                      :subscribable_id="item.id"/>
                  <VideoConference
                      v-else
                      modus="link"
                      :videoConference="item.videoconferences"/>
              </div>

          </div>

    </div>
</template>
<script>
import AgendaItemForm from "./AgendaItemForm";
import AgendaItemMedia from "./AgendaItemMedia";
import Speakers from "./Speakers";
import VideoConference from "./VideoConference";

export default {
    name: 'AgendaItem',
    components: {VideoConference, Speakers, AgendaItemMedia, AgendaItemForm},
    props: {
        agenda_id: {
            type: Number,
        },
        personal_agenda:  {
            type: Boolean,
            default: false
        },
        item: {},
    },
    data () {
        return {
            edit: false,
        };
    },
    methods: {
        async destroy(id){
            try {
                this.location = (await axios.delete('/agendaItems/' + id)).data.message;
            } catch (error) {
                console.log(error);
            }
            this.$parent.loadItems();
        },
        postDate(begin_date, end_date) {
            var start = new Date(begin_date.replace(/-/g, "/"));
            var end = new Date(end_date.replace(/-/g, "/"));
            var dateFormat = {
                hour: '2-digit',
                minute: '2-digit'
            };

            if (start.toDateString() === end.toDateString()) {
                return start.toLocaleString([], dateFormat) + " - " + end.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                return start.toLocaleString([], dateFormat) + " - " + end.toLocaleString([], dateFormat);
            }
        },
        subscribe(item) {
            axios.post('/agendaItemSubscriptions/',{agenda_item_id: item.id})
                .then(response => {
                    this.$parent.loadItems();
                })
                .catch(e => {
                    console.log(e.data.errors);
                });
        },
        unsubscribe(item) {
            axios.delete('/agendaItemSubscriptions/' + item.subscriptions[0].id)
                .then(response => {
                    this.$parent.loadItems();
                })
                .catch(e => {
                    console.log(e.data.errors);
                });
        },
        toggleEdit() {
            this.edit = !this.edit;
            this.$parent.loadItems();
        },
    },
    computed: {
        showAddBtn: function () {
            if (typeof this.agenda_id == 'undefined') {
                return false;
            }
            return  true;
        },
    }


}
</script>
