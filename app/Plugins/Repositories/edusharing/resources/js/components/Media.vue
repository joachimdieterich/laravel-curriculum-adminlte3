<template>
    <div class="col-12">
        <ul class="nav nav-tabs"
            role="tablist">
            <li class="btn btn-sm btn-outline-secondary m-2"
                v-bind:class="[(currentTab === 1) ? 'active' : '']"
                id="edusharing_my_files-nav"
                data-toggle="pill"
                href="#edusharing_my_files"
                role="tab"
                aria-controls="edusharing_my_files"
                aria-selected="true"
                @click="setCurrentTab(1);loader();">
                <i class="fa fa-globe"></i>  {{ trans('global.public_files') }}
            </li>
<!--            <li class="btn btn-sm btn-outline-secondary m-2"
                v-bind:class="[(currentTab === 2) ? 'active' : '']"
                id="edusharing_shared-nav"
                data-toggle="pill"
                href="#edusharing_shared"
                role="tab"
                aria-controls="edusharing_shared"
                aria-selected="true"
                @click="setCurrentTab(2);loader()">
                <i class="fa fa-share-alt"></i>  {{ trans('global.shared_files') }}
            </li>-->
            <li
                class="btn btn-sm btn-outline-secondary m-2 "
                v-bind:class="[(currentTab === 3) ? 'active' : '']"
                id="edusharing_mediathek-nav"
                data-toggle="pill"
                href="#edusharing_mediathek"
                role="tab"
                aria-controls="edusharing_mediathek"
                aria-selected="true"
                @click="setCurrentTab(3);loader()">
                <i class="fa fa-user"></i> {{ trans('global.my_files') }}
            </li>

        </ul>
        <div id="loading" class="overlay text-center" style="width:100% !important;">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>


        <span v-for="media_subscription in media">
            <div v-for="medium in media_subscription"
                 :id="medium.node_id"
                 class="box box-objective pointer my-1"
                 style="height: 300px !important; min-width: 200px !important; padding: 0; background-size: cover;"
                 :style="{'background-image':'url('+href(medium)+')'}"
                 @click="show(medium)">
                <div class="symbol"
                    style="position: absolute;
                    padding: 6px;
                    z-index: 1;
                    width: 30px;
                    height: 40px;
                    top: 0px;
                    font-size: 1.2em;
                    left: 10px;"
                     :style="{'background':'white url('+iconUrl(medium)+') no-repeat center', 'background-size': '24px'}"
                     >
                </div>
<!--                <span
                    v-can="'medium_delete'"
                    class="p-1 pointer_hand"
                    accesskey="" style="position:absolute; top:0px; height: 30px; width:100%;">
                        <button
                            id="delete-navigator-item"
                            type="submit"
                            class="btn btn-danger btn-sm pull-right"
                            @click.stop="unlinkMedium(medium.node_id, medium.value);">
                            <small><i class="fa fa-unlink"></i></small>
                        </button>
                </span>-->

                <span class="bg-white text-center p-1 overflow-auto "
                      style="position:absolute; bottom:0px; height: 150px; width:100%;">
                    <h6 class="events-heading pt-1 hyphens" v-html="medium.title"></h6>
                    <p class=" text-muted small" v-html="medium.description"></p>
                </span>
                <span style="position:absolute; bottom:5px; left:5px; ">
                    <img
                        style="height: 16px; "
                        :src="medium.license.icon"/>
                </span>

            </div>
        </span>
        <div v-if="media !== null" class="row pt-1" style="width:100% !important;">
            <span v-if="[0].length > maxItems">
                <span class="col-6">
                   <button type="button"
                           class="btn btn-block btn-primary"
                           :class="page > 0 ? '' : 'disabled'"
                           @click="lastPage()"><i class="fa fa-arrow-left"></i></button>
               </span>

                <span class="col-6">
                   <button type="button"
                           class="btn btn-block btn-primary"
                           :class="media[0].length == maxItems ? '' : 'disabled'"
                           @click="nextPage()"><i class="fa fa-arrow-right"></i></button>
               </span>
            </span>
        </div>
    </div>
</template>


<script>

    export default {
        props: {
                'model': {},
              },
        data() {
            return {
                media:   null,
                page:    0,
                maxItems: 20,
                errors:  {},
                currentTab: 1,
            }
        },
        methods: {
            async loader() {
                $("#loading").show();
                try {
                    this.media = (await axios.post('/repositorySubscriptions/getMedia', {
                        subscribable_type: this.subscribable_type(),
                        subscribable_id: this.model.id,
                        search: this.model.title,
                        page: this.page,
                        maxItems: this.maxItems,
                        repository: 'edusharing',
                        filter: this.currentTab
                    })).data.message;
                    if (this.media == null){
                        $("#loading").hide();
                    }

                } catch(error) {
                    $("#loading").hide();
                    //this.errors = error.response.data.errors;
                }
            },
            async unlinkMedium(id, value) { //id of external reference and value in db
                try {
                    if (id !== value){
                        if (confirm("Diese Medium ist Teil einer Sammlung. Soll die gesamte Sammlung entfernt werden?") == false) {
                           return;
                        }
                    }
                    await axios.post('/repositorySubscriptions/destroySubscription', {
                                value: value,
                                subscribable_id: this.model.id,
                                subscribable_type: this.subscribable_type(),
                                repository: 'edusharing'
                            }).data;
                } catch(error) {
                    //this.errors = error.response.data.errors;
                }
                $("#"+id).removeClass( "bg-green" );
                $("#link_btn_"+id).removeClass( "invisible" );
                $("#unlink_btn_"+id).addClass( "invisible" );
            },

            subscribable_type() {
                var reference_class = 'App\\TerminalObjective';
                if (typeof this.model.terminal_objective === 'object'){
                    reference_class = 'App\\EnablingObjective';
                }

                return reference_class;
            },

            show(medium) {
                window.open(medium.path, '_blank');
            },
            href(medium) {
                return medium.thumb;
            },
            iconUrl(medium) {
                return medium.iconURL;
            },
            lastPage() {
                this.page = this.page - 1
                if (this.page == -1){
                    this.page = 0;
                } else{
                    this.loader();
                }
            },
            nextPage() {
                this.page = this.page + 1;
                this.loader();
            },
            setCurrentTab(id){
                this.currentTab = id;
            },
        },
        watch: {
            media: function (value, oldValue) {
                $("#loading").hide();
            }
        },

    }
</script>
