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
                    <span class="col-12 mt-2">
                        <span  class="pull-left">
                            <label for="from">{{ trans('global.timeFrom')}}</label>
                             <VueDatePicker
                                 v-model="date_begin"
                                 model-type="yyyy-MM-dd"
                                 format="dd.MM.yyyy"
                                 :teleport="true"
                                 locale="de"
                                 :select-text="trans('global.ok')"
                                 :cancel-text="trans('global.close')"
                                 :clearable="false"
                             />
                        </span>

                        <span  class="pull-right">
                            <label for="to">{{ trans('global.timeTo')}}</label>
                            <VueDatePicker
                                v-model="date_end"
                                model-type="yyyy-MM-dd"
                                format="dd.MM.yyyy"
                                :teleport="true"
                                locale="de"
                                :select-text="trans('global.ok')"
                                :cancel-text="trans('global.close')"
                                :clearable="false"
                            />
                        </span>

                    </span>
                    <span class="col-3 mt-2">
                        <pie-chart id="devices_chart"
                                   title="Devices"
                                   chart="devices"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-3 mt-2">
                        <pie-chart id="browsers_chart"
                                   title="Browser"
                                   chart="browsers"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-3 mt-1">
                       <pie-chart id="organizations_chart"
                                   :title="trans('global.active')+ ' ' + trans('global.organization.title')"
                                   chart="organizations"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                    <span class="col-3 mt-1">
                        <pie-chart id="groups_chart"
                                   :title="trans('global.active')+ ' ' + trans('global.group.title')"
                                   chart="groups"
                                   :date_begin="date_begin"
                                   :date_end="date_end">
                        </pie-chart>
                    </span>
                     <span class="col-3 mt-1">
                       <pie-chart id="achievements_chart"
                                  :title="trans('global.achievement.title')"
                                  chart="achievements"
                                  :date_begin="date_begin"
                                  :date_end="date_end">
                       </pie-chart>
                    </span>
                    <span class="col-3 mt-1">
                           <pie-chart id="certificates_chart"
                                      :title=" trans('global.certificate.title')"
                                      chart="certificates"
                                      :date_begin="date_begin"
                                      :date_end="date_end">
                           </pie-chart>
                    </span>
                    <span class="col-3 mt-1">
                       <pie-chart id="kanbans_chart"
                                  :title=" trans('global.kanban.title')"
                                  chart="kanbans"
                                  :date_begin="date_begin"
                                  :date_end="date_end">
                       </pie-chart>
                    </span>
                     <span class="col-3 mt-1">
                       <pie-chart id="curricula_chart"
                                  :title="trans('global.active')+ ' ' + trans('global.curriculum.title')"
                                  chart="curricula"
                                  :date_begin="date_begin"
                                  :date_end="date_end">
                       </pie-chart>
                     </span>
                     <span class="col-3 mt-1">
                       <pie-chart id="courses_chart"
                                  :title="trans('global.active')+ ' ' + trans('global.course.title')"
                                  chart="courses"
                                  :date_begin="date_begin"
                                  :date_end="date_end">
                       </pie-chart>
                    </span>
                     <span class="col-3 mt-1">
                       <pie-chart id="eventSubscription_chart"
                                  :title="trans('global.eventSubscription.title')"
                                  chart="eventPlugin"
                                  :date_begin="date_begin"
                                  :date_end="date_end">
                       </pie-chart>
                      </span>
                      <span class="col-3 mt-1">
                       <pie-chart id="repositoryPlugin_chart"
                                  :title="trans('global.externalRepositorySubscription.title')"
                                  chart="repositoryPlugin"
                                  :date_begin="date_begin"
                                  :date_end="date_end">
                       </pie-chart>
                     </span>
                     <span class="col-3 mt-1">
                       <pie-chart id="bbbPlugin_chart"
                                  :title="trans('global.videoconference.title')"
                                  chart="bbbPlugin"
                                  :date_begin="date_begin"
                                  :date_end="date_end">
                       </pie-chart>
                    </span>
<!--                    <span class="col-3 mt-1">
                       <pie-chart id="bbbPlugin_chart"
                                  :title="trans('global.videoconference.title')"
                                  chart="bbbPluginParticipants"
                                  :date_begin="date_begin"
                                  :date_end="date_end">
                       </pie-chart>
                    </span>-->
                    <span class="col-12 mt-1">
                        <logins id="login_chart"
                                :date_begin="date_begin"
                                :date_end="date_end"></logins>
                    </span>
                    <span class="col-12 mt-1">
                        <models id="model_chart"
                                chart="model"
                                title="Models"
                                :date_begin="date_begin"
                                :date_end="date_end"></models>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Logins from '../statistic/Logins.vue';
import PieChart from "../statistic/PieChart.vue";
import Models from "../statistic/Models.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';

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
        mounted() {},
        components: {
            VueDatePicker,
            Models,
            PieChart,
            Logins,
        }
    }
</script>
<style>
@media print { }
</style>
