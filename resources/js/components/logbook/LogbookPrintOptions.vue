<template>
    <div id="logbook_print_options"
         class="card col-12">
        <div class="user-block p-2"
        >
            <div class="username ml-0 pt-2">
                <span>{{ trans('global.logbook.print') }}</span>
                <span class="description ml-0">
                  {{ trans('global.selectDateRange') }}
                </span>

                <date-picker
                    class="w-100 pt-2"
                    v-model="time"
                    :shortcuts="shortcuts"
                    range-separator="  -  "
                    type="date" range
                    valueType="YYYY-MM-DD">
                </date-picker>

                <div class="row pt-2 text-muted font-weight-normal">
                    <div class="form-group mb-0 col-sm-6">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showDescription"
                                true-value="true"
                                false-value="false"
                            >
                            <label class="form-check-label">{{ trans('global.logbook.fields.description') }}</label>
                        </div>
                        <div class="form-check"
                             v-permission="'content_access'">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showContents"
                                true-value="true"
                                false-value="false"
                            >
                            <label class="form-check-label">{{ trans('global.content.title') }}</label>
                        </div>
                        <div class="form-check"
                             v-permission="'task_access'">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showTasks"
                                true-value="true"
                                false-value="false"
                            >
                            <label class="form-check-label">{{ trans('global.task.title') }}</label>
                        </div>
                    </div>
                    <div class="form-group mb-0 col-sm-6">
                        <div class="form-check"
                             v-permission="'medium_access'">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showMedia"
                                true-value="true"
                                false-value="false"
                            >
                            <label class="form-check-label">{{ trans('global.media.title') }}</label>
                        </div>
                        <div class="form-check"
                             v-permission="'reference_access'">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showReferences"
                                true-value="true"
                                false-value="false"
                            >
                            <label class="form-check-label">{{
                                    trans('global.terminalObjective.title')
                                }}/{{ trans('global.enablingObjective.title') }}</label>
                        </div>
                        <div class="form-check"
                             v-permission="'absence_access'">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showAbsences"
                                true-value="true"
                                false-value="false"
                            >
                            <label class="form-check-label">{{ trans('global.absences.title') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-2">
             <span class="pull-right">
                     <button class="btn btn-primary" @click="print()">{{ trans('global.logbook.print') }}</button>
                </span>
        </div>

    </div>

</template>
<style>
.mx-datepicker-sidebar {
    width: 160px !important;
}

.mx-datepicker-sidebar + .mx-datepicker-content {
    margin-left: 160px;
}

</style>
<script>
const DatePicker =
    () => import('vue3-datepicker');

export default {
    props: {
        'logbook': Object,
        'period': Object
    },
    data() {
        let self = this; //to use this in nested functions
        return {
            showDescription: "true",
            showContents: "true",
            showTasks: "true",
            showMedia: "true",
            showReferences: "true",
            showAbsences: "true",

            time: null,
            shortcuts: [
                {
                    text: window.trans.global.today,
                    onClick() {
                        return this.time = [new Date(), new Date()];
                    },
                },
                {
                    text: window.trans.global.sinceYesterday,
                    onClick() {
                        return this.time = [moment().add(-1, "days").toDate(), moment().toDate()];
                    },
                },
                {
                    text: window.trans.global.lastWeek,
                    onClick() {
                        return this.time = [moment().add(-7, "days").toDate(), moment().toDate()];
                    },
                },
                {
                    text: window.trans.global.lastMonth,
                    onClick() {
                        return this.time = [moment().add(-1, "month").toDate(), moment().toDate()];
                    },
                },
                {
                    text: window.trans.global.lastYear,
                    onClick() {
                        return this.time = [moment().add(-1, "year").toDate(), moment().toDate()];
                    },
                },
                {
                    text: window.trans.global.currentPeriod,
                    onClick() {
                        return this.time = [moment(self.period.begin).toDate(), moment(self.period.end).toDate()];
                    },
                }
            ],

        };
    },
    methods: {

        print() {
            location.href = '/logbooks/'
                + this.logbook.id
                + '/print?begin='
                + this.time[0]
                + '&end=' + this.time[1]
                + '&showDescription=' + this.showDescription
                + '&showContents=' + this.showContents
                + '&showTasks=' + this.showTasks
                + '&showMedia=' + this.showMedia
                + '&showReferences=' + this.showReferences
                + '&showAbsences=' + this.showAbsences
            ;
        }
    },
    mounted() {
        this.time = [moment(this.period.begin).format("YYYY-MM-DD"), moment(this.period.end).format("YYYY-MM-DD")];
    },
    components: {
        DatePicker
    },
}
</script>


