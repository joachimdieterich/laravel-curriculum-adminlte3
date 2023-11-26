<template >
    <div class="row">
        <div class="col-md-12 ">
            <ul v-if="typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined'"
                class="nav nav-pills py-2" role="tablist">
                <li class="nav-item">
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

        <table id="videoconference-datatable" style="display: none;"></table>
        <div id="videoconference-content" >
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
                         :id="'videoconferenceDropdown_' + videoconference.id"
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
                            <button :name="'videoconference-share_' + videoconference.id"
                                    class="dropdown-item text-secondary"
                                    @click.prevent="shareVideoconference(videoconference)">
                                <i class="fa fa-share-alt mr-2"></i>
                                {{ trans('global.videoconference.share') }}
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
            <videoconference-index-add-widget
                v-if="((this.filter == 'all' && typeof (this.subscribable_type) == 'undefined' && typeof(this.subscribable_id) == 'undefined')|| this.filter  == 'owner') "
                v-can="'videoconference_create'"/>
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
import VideoconferenceIndexAddWidget from "./VideoconferenceIndexAddWidget";
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
            this.$eventHub.$emit('edit_videoconference', videoconference);
            //window.location = "/videoconferences/" + videoconference.id + "/edit";
        },
        shareVideoconference(videoconference){
            this.$modal.show('subscribe-modal', { 'modelId': videoconference.id, 'modelUrl': 'videoconference' , 'shareWithToken': true, 'canEditLabel': 'darf Videokonferenz starten'});
        },
        loaderEvent(){
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/videoconferenceSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            } else {
                this.url = '/videoconferences/list?filter=' + this.filter
            }

            if ($.fn.dataTable.isDataTable( '#videoconference-datatable' )){
                $('#videoconference-datatable').DataTable().ajax.url(this.url).load();
            } else {
                const dtObject = $('#videoconference-datatable').DataTable({
                    ajax: this.url,
                    dom: 'tilpr',
                    pageLength: 50,
                    language: {
                        url: '/datatables/i18n/German.json',
                        paginate: {
                            "first":      '<i class="fa fa-angle-double-left"></id>',
                            "last":       '<i class="fa fa-angle-double-right"></id>',
                            "next":       '<i class="fa fa-angle-right"></id>',
                            "previous":   '<i class="fa fa-angle-left"></id>',
                        },
                    },
                    columns: [
                        { title: 'id', data: 'id' },
                        { title: 'meetingID', data: 'meetingID' },
                        { title: 'meetingName', data: 'meetingName', searchable: true},
                        { title: 'welcomeMessage', data: 'welcomeMessage', searchable: true },
                       /* { title: 'attendeePW', data: 'attendeePW' },
                        { title: 'moderatorPW', data: 'moderatorPW' },
                        { title: 'endCallbackUrl', data: 'endCallbackUrl' },
                        { title: 'dialNumber', data: 'dialNumber' },
                        { title: 'maxParticipants', data: 'maxParticipants' },
                        { title: 'logoutUrl', data: 'logoutUrl' },
                        { title: 'record', data: 'record' },
                        { title: 'duration', data: 'duration' },
                        { title: 'isBreakout', data: 'isBreakout' },
                        { title: 'moderatorOnlyMessage', data: 'moderatorOnlyMessage'},
                        { title: 'autoStartRecording', data: 'autoStartRecording' },
                        { title: 'allowStartStopRecording', data: 'allowStartStopRecording' },
                        { title: 'bannerText', data: 'bannerText' },
                        { title: 'bannerColor', data: 'bannerColor' },
                        { title: 'logo', data: 'logo' },
                        { title: 'copyright', data: 'copyright' },
                        { title: 'muteOnStart', data: 'muteOnStart' },
                        { title: 'allowModsToUnmuteUsers', data: 'allowModsToUnmuteUsers' },
                        { title: 'lockSettingsDisableCam', data: 'lockSettingsDisableCam' },
                        { title: 'lockSettingsDisableMic', data: 'lockSettingsDisableMic' },
                        { title: 'lockSettingsDisablePrivateChat', data: 'lockSettingsDisablePrivateChat' },
                        { title: 'lockSettingsDisablePublicChat', data: 'lockSettingsDisablePublicChat' },
                        { title: 'lockSettingsDisableNote', data: 'lockSettingsDisableNote' },
                        { title: 'lockSettingsLockedLayout', data: 'lockSettingsLockedLayout' },
                        { title: 'lockSettingsLockOnJoin', data: 'lockSettingsLockOnJoin' },
                        { title: 'lockSettingsLockOnJoinConfigurable', data: 'lockSettingsLockOnJoinConfigurable' },
                        { title: 'guestPolicy', data: 'guestPolicy' },
                        { title: 'meetingKeepEvents', data: 'meetingKeepEvents' },
                        { title: 'endWhenNoModerator', data: 'endWhenNoModerator' },
                        { title: 'endWhenNoModeratorDelayInMinutes', data: 'endWhenNoModeratorDelayInMinutes' },
                        { title: 'meetingLayout', data: 'meetingLayout' },
                        { title: 'learningDashboardCleanupDelayInMinutes', data: 'learningDashboardCleanupDelayInMinutes' },
                        { title: 'allowModsToEjectCameras', data: 'allowModsToEjectCameras' },
                        { title: 'allowRequestsWithoutSession', data: 'allowRequestsWithoutSession' },
                        { title: 'userCameraCap', data: 'userCameraCap' },
                        { title: 'allJoinAsModerator', data: 'allJoinAsModerator' },
                        { title: 'medium_id', data: 'medium_id' },
                        { title: 'webcamsOnlyForModerator', data: 'webcamsOnlyForModerator' },
                        { title: 'anyoneCanStart', data: 'anyoneCanStart' },*/
                    ],
                }).on('draw.dt', () => { // checks if the datatable-data changes, to update the videoconference-data
                    this.videoconferences = dtObject.rows({ page: 'current' }).data().toArray();
                    $('#videoconference-content').insertBefore('#videoconference-datatable');
                });
            }
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
        destroy() {
            axios.delete('/videoconferences/' + this.currentVideoconference.id)
                .then(res => {
                    let index = this.videoconferences.indexOf(this.currentVideoconference);
                    this.videoconferences.splice(index, 1);
                })
                .catch(err => {
                    console.log(err.response);
                });
        },
    },
    mounted() {
        if (document.getElementById('searchbar') != null) {
            document.getElementById('searchbar').classList.remove('d-none');
        }

        const filters = ["all", "owner", "shared_with_me", "shared_by_me"];
        let url = new URL(window.location.href);
        let urlFilter = url.searchParams.get("filter");

        if (filters.includes(urlFilter)){
          this.filter = urlFilter
        }

        this.$eventHub.$on('filter', (filter) => {
            $('#videoconference-datatable').DataTable().search(filter).draw();
        });
        this.$eventHub.$on('videoconference-added', (videoconference) => {
            this.videoconferences.push(videoconference);
        });
        this.$eventHub.$on('videoconference-updated', (videoconference) => {
            //console.log(videoconference);
            const index = this.videoconferences.findIndex(
                vc => vc.id === videoconference.id
            );

            for (const [key, value] of Object.entries(videoconference)) {
                this.videoconferences[index][key] = value;
            }
        });
        this.loaderEvent()
    },

    components: {
        VideoconferenceIndexAddWidget,
        Modal
    },
}
</script>
<style>
#videoconference-datatable_wrapper {
    width: 100%;
    padding: 0px 15px;
}
</style>
<style scoped>
.nav-link:hover {
    cursor: default;
    user-select: none;
}

.nav-item:hover .nav-link:not(.active) {
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer;
}
</style>
