<template>
    <modal
        id="plan-achievements-options-modal"
        name="plan-achievements-options-modal"
        height="auto"
        width="650px"
        :adaptive=true
        draggable=".draggable"
        @before-open="beforeOpen"
        @opened="opened()"
        @before-close="beforeClose()"
        style="z-index: 1100"
    >
        <div class="card mb-0" style="max-height: 100svh;">
            <div class="card-header">
                <h3 class="card-title">{{ trans('global.options') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable">
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body overflow-auto">
                <div class="form-group">
                    <span class="font-weight-bold">{{ trans('global.plan.options.timespan') }}</span>
                    <date-picker
                        v-model="options.timespan"
                        class="w-100"
                        :range="true"
                        format="DD.MM.YYYY"
                        value-type="YYYY-MM-DD"
                        :placeholder="trans('global.selectDateRange')"
                        @clear="options.timespan = ['', '']"
                    ></date-picker>
                </div>
                <div class="custom-switch custom-switch-on-green mb-2">
                    <input type="checkbox" id="teacher_toggle" class="custom-control-input" v-model="options.showTeacher" disabled>
                    <label for="teacher_toggle" class="custom-control-label pointer" style="cursor: not-allowed">{{ trans('global.plan.options.toggle_teacher') }}</label>
                </div>
                <div class="custom-switch custom-switch-on-green mb-2">
                    <input type="checkbox" id="student_toggle" class="custom-control-input" v-model="options.showStudent" disabled>
                    <label for="student_toggle" class="custom-control-label pointer" style="cursor: not-allowed">{{ trans('global.plan.options.toggle_student') }}</label>
                </div>
                <div class="custom-switch custom-switch-on-green mb-2">
                    <input type="checkbox" id="unset_toggle" class="custom-control-input" v-model="options.showUnset">
                    <label for="unset_toggle" class="custom-control-label pointer" @click="toggleUnset()">{{ trans('global.plan.options.toggle_unset') }}</label>
                </div>
                <div class="custom-switch custom-switch-on-green">
                    <input type="checkbox" id="objectives_toggle" class="custom-control-input" v-model="options.collapseObjectives">
                    <label for="objectives_toggle" class="custom-control-label pointer" @click="toggleObjectives()">{{ trans('global.plan.options.toggle_objectives') }}</label>
                </div>
            </div>
            <div class="card-footer">
                <span class="d-flex justify-content-between pull-right">
                    <button class="btn btn-primary" @click="close()">{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>
<script>
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    data() {
        return {
            options: {
                timespan: ['', ''],
                hideUnset: false,
                showTeacher: true,
                showStudent: false,
                collapseObjectives: false,
            },
        }
    },
    methods: {
        toggleUnset() {
            // setTimeout is needed because of race condition
            setTimeout(() => {
                this.$parent.toggleUnset(this.options.showUnset);
            }, 50);
        },
        toggleObjectives() {
            setTimeout(() => {
                this.$parent.toggleObjectives(this.options.collapseObjectives);
            }, 50);
        },
        close() {
            this.$modal.hide('plan-achievements-options-modal');
        },
        beforeOpen() {},
        opened() {},
        beforeClose() {},
        closed() {},
    },
    components: {
        DatePicker,
    },
}
</script>