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
                <a class="nav-link link-muted "
                   id="admin-nav-tab"
                   data-toggle="pill"
                   href="#admin-tab"
                   role="tab"
                   aria-controls="admin-tab"
                   aria-selected="true">
                    <i class="fas fa-th"></i>
                </a>
            </li>
           <li class="nav-item">
                <a class="nav-link link-muted"
                   id="metadata-nav-tab"
                   data-toggle="pill"
                   href="#metadata-tab"
                   role="tab"
                   aria-controls="metadata-tab"
                   aria-selected="false"
                   @click="loaderEvent()">
                    <i class="fa fa-barcode pr-2"></i>{{trans('global.metadataset.title')}}
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
                        <input class="pull-right"
                               v-model="date"
                               type="date"/>
                    </span>
                    <span class="col-6 mt-2">
                        <devices :date="date"></devices>
                    </span>
                    <span class="col-6 mt-2">
                        <browser :date="date"></browser>
                    </span>
                    <span class="col-6 mt-1">
                        <organizations :date="date"></organizations>
                    </span>
                    <span class="col-6 mt-1">
                        <groups :date="date"></groups>
                    </span>
                    <span class="col-6 mt-1">
                        <achievements :date="date"></achievements>
                    </span>
                    <span class="col-6 mt-1">
                        <certificates :date="date"></certificates>
                    </span>
                    <span class="col-6 mt-1">
                        <curricula :date="date"></curricula>
                    </span>
                    <span class="col-6 mt-1">
                        <courses :date="date"></courses>
                    </span>
                    <span class="col-6 mt-1">
                        <events :date="date"></events>
                    </span>
                    <span class="col-6 mt-1">
                        <repository :date="date"></repository>
                    </span>
                    <span class="col-12 mt-1">
                        <logins :date="date"></logins>
                    </span>

                </div>

            </div>

            <div class="tab-pane fade pt-2"
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
            </div>
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
                date: new Date().toISOString().slice(0, 10),

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
