<template>
  <div class="timeline">
    <div  v-for="item in items">
         <i :class="item.type.icon_css + ' ' + item.type.color"></i>

          <div class="timeline-item">
                <span class="time">
                    <i class="fas fa-clock"></i>
                    {{ postDate(item.begin, item.end) }}
                </span>
                <h3 class="timeline-header text-bold">
                    <i v-if="item.subscriptions.length > 0 "
                       class="fas fa-star text-primary"></i>
                    <i v-else
                       class="far fa-star text-gray"></i>
                    <a href="#" v-html="item.title"></a>
                </h3>

                <div class="timeline-body"
                     v-html="item.description">
                </div>
                <div class="timeline-footer">
                    <a class="btn btn-primary btn-sm">Read more</a>
                    <a class="btn btn-danger btn-sm">Delete</a>
                    <i class="pull-right fa-solid fa-up-right-from-square text-primary pointer"></i>
                </div>
          </div>
    </div>
    <!-- END timeline item -->

    <div>
      <i class="fas fa-plus bg-gray"></i>
    </div>
  </div>
</template>
<script>
export default {
    name: 'Agenda',
    props: {
        agenda: {},
        subscribed:  {
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
            items: {},
        };
    },
    methods:{
        postDate(begin_date, end_date) {
            var start = new Date(begin_date.replace(/-/g, "/"));
            var end = new Date(end_date.replace(/-/g, "/"));
            var dateFormat = {
               /* weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',*/
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
    },
    mounted() {
        let param = ''
        if (this.subscribed === true){
            param = 'meeting_date_id=' + this.meeting_date_id
            console.log('subscribed');
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
}
</script>
