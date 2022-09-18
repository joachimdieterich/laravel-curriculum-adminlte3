<template>
    <div>
        <div>
            <h4>{{ trans('global.exam.title') }}</h4>
        </div>
        <data-table
            :data="data"
            :columns="columns"
            @on-table-props-changed="reloadTable">
            <div slot="filters" slot-scope="{ perPage, page }">
                <div class="row"
                     style="width:100%; border-radius: 5px; background-color: #009cee; margin-left: 1px; height: 80px">
                    <div class="col-md-12" style="margin-top: 20px">
                        <input
                            name="name"
                            class="form-control"
                            v-model="tableProps.search"
                            placeholder="Search"
                            @input="getData()">
                    </div>
                </div>
            </div>
            <thead slot="header" slot-scope="{ tableProps }">
            <tr>
                <th @click="sort('tool', tableProps)">{{ trans('global.exam.fields.tool') }}</th>
                <th @click="sort('test_name', tableProps)">{{ trans('global.exam.fields.test_booklet') }}</th>
                <th @click="sort('subject', tableProps)">{{ trans('global.exam.fields.subject') }}</th>
                <th>{{ trans('global.exam.fields.status') }}<i id="status_sync_all" class="fa fa-refresh"
                                                               style="margin-left: 10px" @click="getAllStatus()"></i>
                </th>
                <th @click="sort('created_at', tableProps)">{{ trans('global.exam.fields.created_at') }}</th>
                <th>{{ trans('global.exam.fields.action') }}</th>
            </tr>
            </thead>
            <tbody slot="body" slot-scope="{ data }">
            <tr
                :key="exam.id"
                v-for="(exam, key) in data"
                v-if="exam !== 'created_at'">
                <td
                    :key="column.name"
                    v-for="column in columns"
                    v-if="column.name !== 'created_at'">
                    <data-table-cell
                        :value="exam"
                        :name="column.name"
                        :meta="column.meta"
                        :comp="column.component"
                        :classes="column.classes">
                    </data-table-cell>
                </td>
                <td>
                    <progress id="status" :value="exam.status" max="100"> {{ exam.status }}%</progress>
                    <button v-if="exam.status !== 0" type="button" class="btn" @click="getReport(exam)">
                        <i class="fa fa-download" style="color: rgb(87, 93, 100);"></i>
                    </button>
                </td>
                <td>
                    <div>{{ exam.created_at }}</div>
                </td>
                <td>
                    <a :href="'/exam/' + exam.exam_id + '/edit'" class="btn">
                        <i class="fa fa-list-alt"></i>
                    </a>
                    <button v-permission="'test_edit'" type="button" class="btn text-danger"
                            @click="destroyDataTableEntry(key, exam)">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            </tbody>
            <div slot="pagination" slot-scope="{ links = {}, meta = {}, page, perPage}">
                <div class="text-left" style="display: flex">
                    <ul class="pagination">
                        <li class="first">
                            <button
                                :disabled="meta.current_page === 1"
                                class="btn page_links"
                                @click="paginationChangePage(links.first)">
                                <span> First </span>
                            </button>
                        </li>
                        <li class="previous">
                            <button
                                :disabled="!links.prev"
                                class="btn page_links"
                                @click="paginationChangePage(links.prev)">
                                <span> Previous </span>
                            </button>
                        </li>
                        <li v-for="n in meta.last_page">
                            <button
                                v-if="n === meta.current_page
                                 || n === 1
                                 || n < meta.current_page + 2 && n > meta.current_page - 2
                                 || n === meta.last_page"
                                :class="n === meta.current_page ?'active-bold':''"
                                :disabled="!page"
                                class="btn page_links"
                                @click="setPage(n)">
                                <span>{{ n }}</span>
                            </button>
                            <button
                                v-else-if="
                                n === meta.current_page - 2 || n === meta.current_page + 2"
                                class="btn page_links disabled">
                                <span>...</span>
                            </button>
                        </li>
                        <li class="next">
                            <button
                                :disabled="!links.next"
                                class="btn page_links"
                                @click="paginationChangePage(links.next)">
                                <span> Next </span></button>
                        </li>
                        <li class="last">
                            <button
                                :disabled="meta.current_page === meta.last_page"
                                class="btn page_links"
                                @click="paginationChangePage(links.last)">
                                <span> Last </span>
                            </button>
                        </li>
                    </ul>
                    <div class="dataTables_length text-right" style="width: 25%;  margin-left: 50%;">
                        <label>Show
                            <select
                                @change="onChange($event)"
                                style="width: 40%" name="exam-users-datatable_length"
                                aria-controls="exam-users-datatable"
                                class="custom-select custom-select-sm form-control form-control-sm"
                                v-model="tableProps.length">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entries</label>
                    </div>
                </div>
                <nav class="row">
                    <div class="col-md-6 text-left">
                        <span>
                            Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} entries
                        </span>
                    </div>
                </nav>
            </div>
        </data-table>
        <tests-table
            v-permission="'test_create'"
            @addExam="addExam"
            @failedNotification="failedNotification"
            :group_id="this.group_id"></tests-table>
    </div>
</template>

<script>
export default {
    props: {
        group_id: '',
    },
    data() {
        return {
            url: '/exams',
            data: {},
            tableProps: {
                group_id: this.group_id,
                page: 1,
                search: '',
                length: 10,
                column: 'created_at',
                dir: 'asc'
            },
            sortOrders: {},
            columns: [
                {
                    label: 'Tool',
                    name: 'tool',
                    orderable: true,
                },
                {
                    label: 'Test Name',
                    name: 'test_name',
                    orderable: true,
                },
                {
                    label: 'Subject',
                    name: 'subject'
                },
                {
                    label: 'Created At',
                    name: 'created_at',
                    orderable: true,
                }
            ],
            savingSuccessful: false
        }
    },
    mounted() {
        this.getData();
        this.columns.forEach((column) => {
            this.sortOrders[column.name] = -1;
        });
    },
    methods: {
        getData(url = this.url, options = this.tableProps) {
            axios.get(url, {
                params: options
            })
                .then(response => {
                    this.data = response.data;
                })
                .catch(errors => {
                    console.log(errors)
                }).finally(() => {
                this.getAllStatus()
            })
        },
        getAllStatus() {
            this.data.data.map(async exam => {
                await this.getStatus(exam)
            });
            document.getElementById("status_sync_all").classList.remove("fa-spin");
        },
        getReport(exam) {
            axios.post('/exam/' + exam.exam_id + '/report', {tool: exam.tool}, {responseType: 'arraybuffer'})
                .then(response => {
                    var blob = new Blob([response.data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = exam.test_name+'.pdf';
                    link.click();
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
        addExam(exam) {
            if (this.data.data.length < this.tableProps.length) {
                this.data.data.push(exam)
            } else {
                this.reloadTable(this.tableProps)
            }
            this.successNotification(Vue.prototype.trans('global.exam.success_messages.exam_created'))
        },
        destroyDataTableEntry(key, exam) {
            if (confirm(Vue.prototype.trans('global.exam.confirm_messages.confirm_delete'))) {
                axios.delete('/exams/' + exam.exam_id + '?tool=' + exam.tool).then(response => {
                    if (response.status === 200) {
                        Vue.delete(this.data.data, key);
                        this.successNotification(Vue.prototype.trans('global.exam.success_messages.exam_removed'))
                    }
                })
                    .catch(errors => {
                        var message = errors.response.status === 403 ? Vue.prototype.trans('global.exam.error_messages.remove_exam') : errors.message
                        this.failedNotification(message)
                    })
            }
        },
        getStatus(exam) {
            document.getElementById("status_sync_all").classList.add("fa-spin");

            axios.post('/exam/' + exam.exam_id + '/status', {
                tool: exam.tool
            }).then(response => {
                this.data.data.filter(function (item) {
                    if (item === exam) {
                        item.status = response.data
                    }
                });
            })
                .catch(errors => {
                    console.log(errors)
                })
        },
        setPage(n) {
            this.tableProps.page = n
            this.getData();
        },
        onChange(event) {
            this.tableProps.length = event.target.value
            this.getData();
        },
        paginationChangePage(url) {
            let tableProps = this.tableProps
            delete tableProps.page;
            this.getData(url, tableProps);
        },
        reloadTable(tableProps) {
            this.getData(this.url, tableProps);
        },
        sort(key, tableProps) {
            tableProps.group_id = this.group_id
            tableProps.column = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
            tableProps.dir = this.sortOrders[key] === 1 ? 'desc' : 'asc';
        },
        successNotification(message) {
            this.$toast.success(message, {
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false
            });
        },
        failedNotification(message) {
            this.$toast.error(message, {
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false
            });
        }
    }
}

</script>

<style>

thead {
    cursor: pointer;
}

.page_links {
    background-color: transparent;
    color: #737C83 !important;
    border: 1px solid #dee2e6;
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem
}

.active-bold {
    font-weight: 800;
}
</style>



