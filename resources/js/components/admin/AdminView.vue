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
                        <pie-chart id="devices_chart"
                                   title="Devices"
                                   chart="devices"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-2">
                        <pie-chart id="browsers_chart"
                                   title="Browser"
                                   chart="browsers"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-1">
                       <pie-chart id="organizations_chart"
                                   :title="trans('global.active')+ ' ' + trans('global.organization.title')"
                                   chart="organizations"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-1">
                        <pie-chart id="groups_chart"
                                   :title="trans('global.active')+ ' ' + trans('global.group.title')"
                                   chart="groups"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-1">
                        <pie-chart id="achievements_chart"
                                   :title="trans('global.achievement.title')"
                                   chart="achievements"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-1">
                        <pie-chart id="certificates_chart"
                                   :title=" trans('global.certificate.title')"
                                   chart="certificates"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-1">
                        <pie-chart id="curricula_chart"
                                   :title="trans('global.active')+ ' ' + trans('global.curriculum.title')"
                                   chart="curricula"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-1">
                        <pie-chart id="courses_chart"
                                   :title="trans('global.active')+ ' ' + trans('global.course.title')"
                                   chart="courses"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-1">
                        <pie-chart id="eventSubscription_chart"
                                   :title="trans('global.eventSubscription.title')"
                                   chart="eventPlugin"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-6 mt-1">
                        <pie-chart id="repositoryPlugin_chart"
                                   :title="trans('global.externalRepositorySubscription.title')"
                                   chart="repositoryPlugin"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-12 mt-1">
                        <logins id="login_chart"
                                :date_begin="date_begin"
                                :date_end="date_end"></logins>
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
    import Logins from '../statistic/Logins';
    import PieChart from "../statistic/PieChart";

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
            PieChart,
            Logins,
        }

    }
</script>
<style>
@media print {

}
</style>
