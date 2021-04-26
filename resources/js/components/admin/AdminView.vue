<template>
    <div class="col-12">
        <ul class="nav nav-tabs no-print"
            role="tablist">
            <li class="nav-item">
                <a class="nav-link link-muted active"
                   id="statistic-nav-tab"
                   data-toggle="pill"
                   href="#statistic-tab"
                   role="tab"
                   aria-controls="statistic-tab"
                   aria-selected="false"
                   @click="loaderEvent('statistics')">
                    <i class="fa fa-chart-pie pr-2"></i>{{trans('global.dashboard.statistic')}}
                </a>
            </li>
           <li class="nav-item">
                <a class="nav-link link-muted"
                   id="metadata-nav-tab"
                   href="/metadatasets">
                    <i class="fa fa-barcode pr-2"></i>{{trans('global.metadataset.title')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link link-muted "
                   id="admin-nav-tab"
                   href="/configs">
                    <i class="fa fa-cogs pr-2"></i>{{trans('global.config.title')}}
                </a>
            </li>
        </ul>

        <div class="tab-content"
             id="custom-content-below-tabContent">

            <div class="tab-pane fade show active "
                 id="statistic-tab"
                 role="tab"
                 aria-labelledby="statistic-nav-tab">

                <div class="row">
                    <span media="print"
                          class="col-12 mt-2">
                        <span  class="pull-left">
                            <label for="from">{{ trans('global.timeFrom')}}</label>
                            <input id="from"
                                   v-model="date_begin"
                                   type="date"/>
                        </span>
                        <span  class="pull-right">
                            <label for="to">{{ trans('global.timeTo')}}</label>
                            <input id="to"
                                   v-model="date_end"
                                   type="date"/>
                        </span>

                    </span>
                    <span class="col-6 mt-2">
                        <devices :date_begin="date_begin" :date_end="date_end"></devices>
                    </span>
                    <span class="col-6 mt-2">
                        <browser :date_begin="date_begin" :date_end="date_end"></browser>
                    </span>
                    <span class="col-6 mt-1">
                        <organizations :date_begin="date_begin" :date_end="date_end"></organizations>
                    </span>
                    <span class="col-6 mt-1">
                        <groups :date_begin="date_begin" :date_end="date_end"></groups>
                    </span>
                    <span class="col-6 mt-1">
                        <achievements :date_begin="date_begin" :date_end="date_end"></achievements>
                    </span>
                    <span class="col-6 mt-1">
                        <certificates :date_begin="date_begin" :date_end="date_end"></certificates>
                    </span>
                    <span class="col-6 mt-1">
                        <curricula :date_begin="date_begin" :date_end="date_end"></curricula>
                    </span>
                    <span class="col-6 mt-1">
                        <courses :date_begin="date_begin" :date_end="date_end"></courses>
                    </span>
                    <span class="col-6 mt-1">
                        <events :date_begin="date_begin" :date_end="date_end"></events>
                    </span>
                    <span class="col-6 mt-1">
                        <repository :date_begin="date_begin" :date_end="date_end"></repository>
                    </span>
                    <span class="col-12 mt-1">
                        <logins :date_begin="date_begin" :date_end="date_end"></logins>
                    </span>

                </div>

            </div>

<!--            <div class="tab-pane fade pt-2"
                 id="admin-tab"
                 role="tabpanel"
                 aria-labelledby="curriculum-nav-tab">
                admintab
            </div>
            <div class="tab-pane fade "
                 id="metadata-tab"
                 role="tab"
                 aria-labelledby="metadata-nav-tab">
                metadata
            </div>-->
        </div>
    </div>
</template>

<script>
    import Browser from '../statistic/Browser';
    import Logins from '../statistic/Logins';
    import Devices from '../statistic/Devices';
    import Curricula from '../statistic/Curricula';
    import Courses from '../statistic/Courses';
    import Events from '../statistic/Events';
    import Organizations from '../statistic/Organizations';
    import Groups from '../statistic/Groups';
    import Achievements from "../statistic/Achievements";
    import Certificates from "../statistic/Certificates";
    import Repository from "../statistic/Repository";

    export default {
        props: {

        },
        data () {
            return {
                date_begin: new Date().toISOString().slice(0, 10),
                date_end:   new Date().toISOString().slice(0, 10),

            };
        },

        methods: {
            loaderEvent: function(func) {
                //alert(func);
            }

        },
        mounted() {

        },
        components: {
            Repository,
            Certificates,
            Achievements,
            Browser,
            Logins,
            Devices,
            Curricula,
            Courses,
            Events,
            Organizations,
            Groups,
        }

    }
</script>
<style>
@media print {

}
</style>
