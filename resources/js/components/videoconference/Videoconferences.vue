<template >
    <div class="row">
        <div class="col-md-12 py-2">
<!--            <div id="videoconferences_filter" class="dataTables_filter">
                <label >
                    <input type="search"
                           class="form-control form-control-sm"
                           placeholder="Suchbegriff"
                           v-model="search">
                </label>
            </div>-->
            <ul v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills" role="tablist">
                <li v-can="'videoconference_create'"
                    class="nav-item ">
                    <a class="nav-link active bg-green"
                       href="/videoconferences/create"
                       id="custom-tabs-create-videoconference"
                    >
                        <i class="fa fa-plus pr-2"></i> {{ trans('global.videoconference.create') }}
                    </a>
                </li>
                <li class="nav-item ml-auto">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="videoconference-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fas fa-chalkboard-teacher pr-2"></i>Alle Videokonferenzen
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user  pr-2"></i>Meine Videokonferenzen
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_with_me' ? 'active' : ''"
                       id="custom-filter-shared-with-me"
                       @click="setFilter('shared_with_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-paper-plane pr-2"></i>Für mich freigegeben
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-share-nodes  pr-2"></i>Von mir freigegeben
                    </a>
                </li>

            </ul>
        </div>

        <div class="col-md-12 py-2">
            <div v-for="videoconference in videoconferences"
                 v-if="(videoconference.meetingName.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3"
                 :id="videoconference.id"
                 v-bind:value="videoconference.id"
                 class="box box-objective nav-item-box-image pointer my-1 "
                 style="min-width: 200px !important;"
                 :style="'border-bottom: 5px solid ' + videoconference.bannerColor"
            >
                <a :href="'/videoconferences/' + videoconference.id"
                   class="text-decoration-none text-black"
                >
                    <div v-if="videoconference.medium_id"
                         class="nav-item-box-image-size"
                         :style="{'background': 'url(/media/' + videoconference.medium_id + '?model=Videoconference&model_id=' + videoconference.id +') top center no-repeat', 'background-size': 'cover', }">
                        <div class="nav-item-box-image-size"
                             style="width: 100% !important;"
                             :style="{backgroundColor: videoconference.bannerColor + ' !important',  'opacity': '0.5'}">
                        </div>
                    </div>
                    <div v-else
                         class="nav-item-box-image-size text-center"
                         :style="{backgroundColor: videoconference.bannerColor + ' !important'}">
<!--                        <i class="fa fa-2x p-5 fa-video nav-item-text text-white"></i>-->
                    </div>
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ videoconference.meetingName }}
                   </h1>
                   <p class="text-muted small"
                      v-html="decodeHtml(videoconference.welcomeMessage)">
                   </p>
                </span>
                    <div class="symbol"
                         :style="'color:' + $textcolor(videoconference.bannerColor) + '!important'"
                         style="position: absolute; width: 30px; height: 40px;">
                        <i class="fa fa-video pt-2"></i>
                    </div>
                    <div v-if="$userId == videoconference.owner_id"
                         class="btn btn-flat pull-right"
                         :id="'videoconferebceDropdown_' + videoconference.id"
                         style="position:absolute; top:0; right: 0; background-color: transparent;"
                         data-toggle="dropdown"
                         aria-expanded="false"
                        >
                        <i class="fas fa-ellipsis-v"
                           :style="'color:' + $textcolor(videoconference.bannerColor)"></i>
                        <div class="dropdown-menu dropdown-menu-right"
                             style="z-index: 1050;"
                             x-placement="left-start">
                            <button :name="'videoconference-edit_' + videoconference.id"
                                    class="dropdown-item text-secondary"
                                    @click.prevent="editVideoconference(videoconference)">
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.videoconference.edit') }}
                            </button>
                            <hr class="my-1">
                            <button
                                :id="'delete-videoconference-' + videoconference.id"
                                type="submit"
                                class="dropdown-item py-1 text-red"
                                @click.prevent="confirmItemDelete(videoconference)">
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.videoconference.delete') }}
                            </button>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <Modal
            :id="'videoconferenceModal'"
            css="danger"
            :title="trans('global.videoconference.delete')"
            :text="trans('global.videoconference.delete_helper')"
            :ok_label="trans('global.videoconference.delete')"
            v-on:ok="destroy()"
        />
    </div>
</template>

<script>
const Modal =
    () => import('./../uiElements/Modal');

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
            url: '/videoconferences/list',
            errors: {},
            currentVideoconference: {},
            filter: 'all'
        }
    },
    methods: {
        confirmItemDelete(videoconference){
            $('#videoconferenceModal').modal('show');
            this.currentVideoconference = videoconference;
        },
        editVideoconference(videoconference){
            window.location = "/videoconferences/" + videoconference.id + "/edit";
        },
        loaderEvent(){
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/videoconferenceSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/videoconferences/list?filter=' + this.filter
            }
            axios.get(this.url)
                .then(response => {
                    this.videoconferences = response.data.data;
                })
                .catch(e => {
                    console.log(e);
                });
        },
        setFilter(filter){
            this.filter = filter;
            this.loaderEvent();
        },
        decodeHtml(html) {
            let txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value.replace(/(<([^>]+)>)/ig,"");
        },
        async destroy() {
            try {
                this.videoconferences = (await axios.delete('/videoconferences/' + this.currentVideoconference.id)).data.data;
            } catch (error) {
                console.log(error);
            }
            window.location = "/videoconferences";
        },
    },
    created() {
        document.getElementById('searchbar').classList.remove('d-none');
    },

    mounted() {
        const filters = ["all", "owner", "shared_with_me", "shared_by_me"];

        let url = new URL(window.location.href);
        let urlFilter = url.searchParams.get("filter");

        if (filters.includes(urlFilter)){
          this.filter = urlFilter
        }

        this.$eventHub.$on('filter', (filter) => {
            this.search = filter;
        });

        this.loaderEvent();
    },

    components: {
        Modal
    },
}
</script>
