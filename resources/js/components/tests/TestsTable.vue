<template>
    <div>
        <label class="typo__label">{{ trans('global.exam.select_tests') }}</label>
        <multiselect
            ref="value"
            v-model="value"
            tag-placeholder="Create from Tests"
            placeholder="Search or add a Test"
            label="name"
            track-by="id"
            :options="options"
            :multiple="true"
            :taggable="true">
        </multiselect>
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <button
                    id="add-exam"
                    class="btn btn-success"
                    @click="this.CreateExam"
                >
                    {{ trans('global.exam.create') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>

import multiselect from 'vue-multiselect'

export default {
    props: {
        group_id: '',
    },
    components: {
        multiselect
    },
    data() {
        return {
            value: [],
            options: []
        }
    },
    created() {
        this.getData(this.url);
    },
    methods: {
        clearSelect() {
            this.value = [];
            this.$refs.value.deactivate();
        },
        getData() {
            axios.get('/tests')
                .then(response => {
                    this.options = response.data
                })
                .catch(errors => {
                    if (errors.response.status !== 403) {
                        this.$emit('failedNotification', Vue.prototype.trans('global.exam.error_messages.get_tests'))
                    }
                })
        },
        CreateExam() {
            this.value.map(test => {
                this.SendCreateExamRequest(test.tool, test.id, test.nameLong, this.group_id)
            })
        },
        async SendCreateExamRequest(tool, test_id, test_name, group_id) {
            this.clearSelect()
            await axios.post('/exams', {'tool': tool, 'test_id': test_id, 'test_name': test_name, 'group_id': group_id})
                .then(response => {
                    this.$emit('addExam', response.data)
                })
                .catch(errors => {
                    this.$emit('failedNotification', errors)
                })
        }
    },
}
</script>
