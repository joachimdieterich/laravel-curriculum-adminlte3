<template >
    <div class="row">
        <div class="col-md-12 py-2">
            <div id="videoconferences_filter" class="dataTables_filter">
                <label >
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search">
                </label>
            </div>
        </div>

        <div class="col-md-12 py-2">
            <div v-for="(item,index) in subscriptions"
                 v-if="(item.videoconference.meetingName.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
                 :id="item.videoconference.id"
                 v-bind:value="item.videoconference.id"
                 class="box box-objective nav-item-box-image pointer my-1"
                 style="min-width: 200px !important;"
                 :style="'border-bottom: 5px solid ' + item.color"
            >
                <a :href="'/videoconferences/'+item.videoconference.id"
                   class="text-decoration-none text-black"
                >
                    <div class="nav-item-box-image-size text-center"
                         :style="{backgroundColor: item.videoconference.bannerColor + ' !important'}">
                        <i class="fa fa-2x p-5 fa-video nav-item-text text-white"></i>
                    </div>
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ item.videoconference.meetingName }}
                   </h1>
                   <p class="text-muted small"
                      v-html="decodeHtml(item.videoconference.welcomeMessage)">
                   </p>
                </span>
                    <div class="symbol"
                         style="position: absolute;
                            padding: 6px;
                            z-index: 1;
                            width: 30px;
                            height: 40px;
                            background-color: #0583C9;
                            top: 0px;
                            font-size: 1.2em;
                            left: 10px;">
                        <i class="fa fa-video text-white pt-2"></i>
                    </div>
                    <span v-if="$userId == item.videoconference.owner_id"
                          class="p-1 pointer_hand"
                          accesskey="" style="position:absolute; top:0px; height: 30px; width:100%;">

<!--                       <button
                           :id="'delete-item.videoconference-'+item.videoconference.id"
                           type="submit" class="btn btn-danger btn-sm pull-right"
                           @click.prevent="confirmItemDelete(item.videoconference.id)">
                           <small><i class="fa fa-trash"></i></small>
                       </button>-->

                       <a :href="'/item.videoconferences/' + item.videoconference.id + '/edit'"
                          class="btn btn-primary btn-sm pull-right mr-1">
                           <small><i class="fa fa-pencil-alt"></i></small>
                       </a>
                   </span>
                </a>


            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            videoconferences: [],
            subscriptions: {},
            search: '',
            errors: {}
        }
    },
    methods: {
        loaderEvent(){
            axios.get('/videoconferenceSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
                .then(response => {
                    this.subscriptions = response.data.subscriptions;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        decodeHtml(html) {
            let txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value.replace(/(<([^>]+)>)/ig,"");
        },
    },

    mounted() {
        this.loaderEvent();
    },
    components: {

    },
}
</script>
