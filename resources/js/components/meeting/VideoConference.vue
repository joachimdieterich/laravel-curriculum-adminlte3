<template>
    <span v-if="modus == 'Button'">
            <a class="pull-right btn btn-primary btn-sm "
               @click="submit()"
            > +</a>
    </span>
    <span v-else>
          <a class="pull-right btn btn-primary btn-sm "
             :href="'/videoconferences/' + videoConference.id"
             target="_blank"
          >
            <i class="fa-solid fa-up-right-from-square"></i>
            Link zum Konferenzraum</a><span class="clearfix"></span>
    </span>
</template>
<script>
import Form from "form-backend-validation";

export default {
    name: 'VideoConference',
    props: {
        videoConference: {},
        modus: {
            type: String,
            default: 'Button'
        },
        subscribable_type: {
            type: String,
            default: 'AgendaItem'
        },
        subscribable_id: {
            type: Number,
        },
        meetingID: {
            type: String,
            default: ''
        },
        meetingName: {
            type: String,
            default: ''
        },
        attendeePW: {
            type: String,
            default: ''
        },
        moderatorPW: {
            type: String,
            default: ''
        },
        endCallbackUrl: {
            type: String,
            default: ''
        },
        logoutUrl: {
            type: String,
            default: ''
        }
    },
    data () {
        return {
            edit: false,
            conference: '',
            form: new Form({
                'id':'',
                'meetingID': this.meetingID,
                'meetingName': this.meetingName,
                'attendeePW': this.attendeePW,
                'moderatorPW': this.moderatorPW,
                'endCallbackUrl': this.endCallbackUrl,
                'logoutUrl': this.logoutUrl,
                'subscribable_type': this.subscribable_type,
                'subscribable_id': this.subscribable_id,
            }),
        };
    },
    methods: {
        async submit() {
            try {
                if (this.method === 'patch') {
                    this.conference = (await axios.patch('/videoconferences/' + this.form.id, this.form)).data.videoconference;
                } else {
                    this.conference = (await axios.post('/videoconferences', this.form)).data.videoconference;
                }
            } catch (error) {
                this.form.errors = error.response.data.form.errors;
            }
        },
    }
}
</script>
