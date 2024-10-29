<template>
    <div class="row">
        <div  v-if="!subscribable"
              class="col-md-12 py-2">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link " :class="filter === 'all' ? 'active' : ''" id="logbook-filter-all"
                       @click="setFilter('all')" data-toggle="pill" role="tab">
                        <i class="fa fa-columns pr-2"></i>Alle Logb&uuml;cher
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="filter === 'owner' ? 'active' : ''" id="custom-filter-owner"
                       @click="setFilter('owner')" data-toggle="pill" role="tab">
                        <i class="fa fa-logbook  pr-2"></i>Meine Logb&uuml;cher
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="filter === 'shared_with_me' ? 'active' : ''"
                       id="custom-filter-shared-with-me" @click="setFilter('shared_with_me')" data-toggle="pill"
                       role="tab">
                        <i class="fa fa-paper-plane pr-2"></i>Für mich freigegeben
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="filter === 'shared_by_me' ? 'active' : ''" id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')" data-toggle="pill" role="tab">
                        <i class="fa fa-share-nodes  pr-2"></i>Von mir freigegeben
                    </a>
                </li>

            </ul>
        </div>

        <div class="col-md-12 py-2">
            <IndexWidget
                v-permission="'logbook_create'"
                key="logbookCreate"
                modelName="Logbook"
                url="/logbooks"
                :create=true
                :createLabel="trans('global.logbook.' + create_label_field)">
                <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                       class="fa fa-2x fa-link text-muted"
                    ></i>
                </template>
            </IndexWidget>
            <IndexWidget
                v-for="logbook in logbooks"
                :key="'logbookIndex'+logbook.id"
                :model="logbook"
                modelName="logbook"
                url="/logbooks">
                <template v-slot:itemIcon>
                    <i class="fa-2x" :class="logbook.css_icon"></i>
                </template>
                <template
                    v-permission="'logbook_edit, logbook_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-if="!subscribable"
                            v-permission="'logbook_edit'"
                            :name="'edit-logbook-' + logbook.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editLogbook(logbook)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.logbook.edit') }}
                        </button>
                        <hr v-if="!subscribable"
                            class="my-1">
                        <button
                            v-if="subscribable"
                            v-permission="'logbook_delete'"
                            :id="'expel-logbook-' + logbook.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(logbook)">
                            <i class="fa fa-unlink mr-2"></i>
                            {{ trans('global.logbook.expel') }}
                        </button>
                        <button
                            v-else
                            v-permission="'logbook_delete'"
                            :id="'delete-logbook-' + logbook.id"
                            type="submit"
                            class="dropdown-item py-1 text-red"
                            @click.prevent="confirmItemDelete(logbook)">
                            <span v-if="create_label_field == 'enrol'">
                                 <i class="fa fa-unlink mr-2"></i>
                                {{ trans('global.logbook.expel') }}
                            </span>
                             <span v-else>
                                 <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.logbook.delete') }}
                            </span>
                        </button>
                    </div>
                </template>
            </IndexWidget>
        </div>

        <div id="logbook-datatable-wrapper"
             class="w-100 dataTablesWrapper">
            <DataTable
                id="logbook-datatable"
                :columns="columns"
                :options="options"
                :ajax="url"
                :search="search"
                width="100%"
                style="display:none; "
            ></DataTable>
        </div>

        <Teleport to="body">
            <SubscribeLogbookModal
                v-if="subscribable"
            ></SubscribeLogbookModal>
            <LogbookModal
                v-if="!subscribable"
            ></LogbookModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.logbook.' + delete_label_field)"
                :description="trans('global.logbook.' + delete_label_field +'_helper')"

                :ok_label="trans('trans.global.ok')"
                :cancel_label="trans('trans.global.cancel')"
                @close="() => {
                    this.showConfirm = false;
                }"
                @confirm="() => {
                    this.showConfirm = false;
                    this.destroy();
                }"
            ></ConfirmModal>
        </Teleport>
   </div>
</template>
<script>
import IndexWidget from "../uiElements/IndexWidget.vue";
import LogbookModal from "./LogbookModal.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal.vue";
import SubscribeLogbookModal from "./SubscribeLogbookModal.vue";
import {useDatatableStore} from "../../store/datatables";
import {useGlobalStore} from "../../store/global";
DataTable.use(DataTablesCore);

export default {
    props: {
        reference : Object,
        subscribable: {
            type: Boolean,
            default: false
        },
        create_label_field: {
            type: String,
            default: 'enrol'
        },
        delete_label_field: {
            type: String,
            default: 'delete'
        },
        subscribable_type: '',
        subscribable_id: '',
    },
    setup () { //https://pinia.vuejs.org/core-concepts/getters.html#passing-arguments-to-getters
        const globalStore = useGlobalStore();
        return {
            globalStore
        }
    },
    data() {
        return {
            component_id: this.$.uid,
            logbooks: [],
            subscriptions: {},
            search: '',
            showConfirm: false,
            url: (this.subscribable_id) ? '/logbooks/list?group_id=' + this.reference.id : '/logbooks/list', // if subscibable == true get enrolled logbooks
            errors: {},
            currentLogbook: {},
            columns: [
                { title: 'check', data: 'check' },
                { title: 'id', data: 'id' },
                { title: 'title', data: 'title', searchable: true},
                { title: 'description', data: 'description', searchable: true},
                { title: 'medium_id', data: 'medium_id'},
            ],
            options : this.$dtOptions,
            filter: 'all'
        }
    },
    mounted() {
        this.$eventHub.emit('showSearchbar', true);

        this.loaderEvent();

        this.$eventHub.on('filter', (filter) => {
            this.search = filter;
        });
        this.$eventHub.on('logbook-added', (logbook) => {
            if (!this.subscribable) {
                this.globalStore?.closeModal('logbook-modal');
            } else {
                this.globalStore?.closeModal('subscribe-logbook-modal');
            }
            this.logbooks.push(logbook);
        });
        this.$eventHub.on('logbook-updated', (logbook) => {
            this.globalStore?.closeModal('logbook-modal');
            //this.loaderEvent();
            this.update(logbook); //todo -> use global widget to get update working
        });

        this.$eventHub.on('logbook-subscription-added', () => {
            this.globalStore?.closeModal('subscribe-logbook-modal');
            this.loaderEvent();
        });
        this.$eventHub.on('createLogbook', () => {
            if (!this.subscribable) {
                this.globalStore?.showModal('logbook-modal', {});
            } else {
                this.globalStore?.showModal('subscribe-logbook-modal', {
                    'reference': this.reference,
                    'subscribable_type': this.subscribable_type,
                    'subscribable_id': this.subscribable_id,
                });
            }
        });
    },
    methods: {
        loaderEvent(){
            const dt = $('#logbook-datatable').DataTable();
            dt.on('draw.dt', () => { // checks if the datatable-data changes, to update the curriculum-data
                this.logbooks = dt.rows({page: 'current'}).data().toArray();

                $('#logbook-content').insertBefore('#logbook-datatable-wrapper');
            });
            this.$eventHub.on('filter', (filter) => {
                dt.search(filter).draw();
            });
        },
        editLogbook(logbook){
            this.globalStore?.showModal('logbook-modal', logbook);
        },
        confirmItemDelete(logbook){
            this.currentLogbook = logbook;
            this.showConfirm = true;
        },
        destroy() {
            if (this.subscribable === true)
            {
                axios.post('/logbookSubscriptions/expel', {
                        'model_id' : this.currentLogbook.id,
                        'subscribable_type' : this.subscribable_type,
                        'subscribable_id' : this.subscribable_id,
                    })
                    .then(r => {
                        let index = this.logbooks.indexOf(this.currentLogbook);
                        this.logbooks.splice(index, 1);
                        //this.toast.success(r);
                    })
                    .catch(e => {
                        //this.toast.error(e);
                    });
            }
            else
            {
                axios.delete('/logbooks/' + this.currentLogbook.id)
                    .then(() => {
                        let index = this.logbooks.indexOf(this.currentLogbook);
                        this.logbooks.splice(index, 1);
                    })
                    .catch ((e) => {
                        console.log(e);
                    });
            }

        },
        update(logbook) {
            const index = this.logbooks.findIndex(
                c => c.id === logbook.id
            );

            for (const [key, value] of Object.entries(logbook)) {
                this.logbooks[index][key] = value;
            }
        },
        setFilter(filter) {
            this.filter = filter;
            this.loaderEvent();
        },
    },

    components: {
        ConfirmModal,
        SubscribeLogbookModal,
        DataTable,
        IndexWidget,
        LogbookModal
    },
}
</script>
