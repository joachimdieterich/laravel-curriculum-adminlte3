<template>
    <div id="logbook_print_options"
         class="card col-12">
        <div class="user-block p-2"
        >
            <div class="username ms-0 pt-2">
                <span>{{ trans('global.logbook.print') }}</span>
                <span class="description ms-0 mt-1">{{ trans('global.selectDateRange') }}</span>

                <VueDatePicker
                    v-model="time"
                    :range="{ partialRange: false }"
                    format="dd.MM.yyyy"
                    :teleport="true"
                    locale="de"
                    :placeholder="trans('global.selectDateRange')"
                    :select-text="trans('global.ok')"
                    :cancel-text="trans('global.close')"
                    @cleared="form.date = ['', '']"
                />

                <div class="row pt-2 text-muted font-weight-normal">
                    <div class="form-group mb-0 col-sm-6">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showDescription"
                                :true-value="true"
                                :false-value="false"
                            >
                            <label class="form-check-label">{{ trans('global.logbook.fields.description') }}</label>
                        </div>
                        <div class="form-check"
                             v-permission="'content_access'">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showContents"
                                :true-value="true"
                                :false-value="false"
                            >
                            <label class="form-check-label">{{ trans('global.content.title') }}</label>
                        </div>
                        <div class="form-check"
                             v-permission="'task_access'">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showTasks"
                                :true-value="true"
                                :false-value="false"
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
                                :true-value="true"
                                :false-value="false"
                            >
                            <label class="form-check-label">{{ trans('global.medium.title') }}</label>
                        </div>
                        <div class="form-check"
                             v-permission="'reference_access'">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                v-model="showReferences"
                                :true-value="true"
                                :false-value="false"
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
                                :true-value="true"
                                :false-value="false"
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
<script>
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
    components: { VueDatePicker },
    props: {
        'logbook': Object,
        'period': Object
    },
    data() {
        return {
            showDescription: true,
            showContents: true,
            showTasks: true,
            showMedia: true,
            showReferences: true,
            showAbsences: true,
            time: ['', ''],
        };
    },
    methods: {
        print() {
            location.href = '/logbooks/'
                + this.logbook.id
                + '/print?begin=' + this.time[0]
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
}
</script>