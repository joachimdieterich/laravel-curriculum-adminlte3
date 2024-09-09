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
                key="'logbookCreate'"
                modelName="Logbook"
                url="/logbooks"
                :create=true
                :createLabel="trans('global.logbook.' + create_label_field)">
            <template v-slot:itemIcon>
                    <i v-if="create_label_field == 'enrol'"
                       class="fa fa-2x p-5 fa-link nav-item-text text-muted"></i>
                    <i v-else
                       class="fa fa-2x p-5 fa-plus nav-item-text text-muted"></i>
                </template>
            </IndexWidget>
            <IndexWidget
                v-for="logbook in logbooks"
                :key="'logbookIndex'+logbook.id"
                :model="logbook"
                modelName= "logbook"
                url="/logbooks">
                <template v-slot:icon>
                    <i class="fas fa-logbook pt-2"></i>
                </template>

                <template
                    v-permission="'logbook_edit, logbook_delete'"
                    v-slot:dropdown>
                    <div class="dropdown-menu dropdown-menu-right"
                         style="z-index: 1050;"
                         x-placement="left-start">
                        <button
                            v-permission="'logbook_edit'"
                            :name="'edit-logbook-' + logbook.id"
                            class="dropdown-item text-secondary"
                            @click.prevent="editLogbook(logbook)">
                            <i class="fa fa-pencil-alt mr-2"></i>
                            {{ trans('global.logbook.edit') }}
                        </button>
                        <hr class="my-1">
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
                <template v-slot:content>
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                   <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                       {{ logbook.title }}
                   </h1>
                   <p class="text-muted small"></p>
                </span>
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
                :show="this.showLogbookModal"
                @close="this.showLogbookModal = false"
                :params="reference"
                :subscribable_type="this.subscribable_type"
                :subscribable_id="this.subscribable_id"
            >
            </SubscribeLogbookModal>
            <LogbookModal
                v-if="!subscribable"
                :show="this.showLogbookModal"
                @close="this.showLogbookModal = false"
                :params="currentLogbook"
            ></LogbookModal>
            <ConfirmModal
                :showConfirm="this.showConfirm"
                :title="trans('global.logbook.' + delete_label_field)"
                :description="trans('global.logbook.' + delete_label_field +'_helper')"
                css= 'danger'
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
import { nextTick } from 'vue';
import IndexWidget from "../uiElements/IndexWidget";
import LogbookModal from "./LogbookModal";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import ConfirmModal from "../uiElements/ConfirmModal";
import SubscribeLogbookModal from "./SubscribeLogbookModal";
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

    data() {
        return {
            component_id: this.$.uid,
            logbooks: [],
            subscriptions: {},
            search: '',
            showLogbookModal: false,
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
            this.searchContent();
        });
        this.$eventHub.on('logbook-added', (logbook) => {
            this.showUserModal = false;
            this.logbooks.push(logbook);
        });


        this.$eventHub.on('logbook-subscription-added', () => {
            this.showLogbookModal = false;
            this.loaderEvent();
        });
        this.$eventHub.on('createLogbook', () => {
            this.currentLogbook = {};
            this.showLogbookModal = true;
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
            this.currentLogbook = logbook;
            this.showLogbookModal = true;
        },
        confirmItemDelete(logbook){
            this.currentLogbook = logbook;
            this.showConfirm = true;
        },
        destroy() {
            if (this.subscribable === true)
            {
                axios.delete('/logbookSubscriptions/' + this.currentLogbook.id)
                    .then(r => {
                        let index = this.logbooks.indexOf(this.currentLogbook);
                        this.logbooks.splice(index, 1);
                    })
                    .catch(err => {
                        console.log(err.response);
                    });
            }
            else
            {
                axios.delete('/logbooks/' + this.currentLogbook)
                    .then(() => {
                        let index = this.logbooks.indexOf(this.currentLogbook);
                        this.logbooks.splice(index, 1);
                    })
                    .catch ((e) => {
                        console.log(e);
                    });
            }

        },



        /*loaderEvent() {
            axios.get('/logbooks/list?filter=' + this.filter)
                .then(async response => {
                    this.logbooks = response.data.data;

                    /!*await nextTick();
                    if (this.search != '') this.searchContent();*!/
                })
                .catch(e => {
                    this.errors = e.data.errors;
                });
        },*/
        setFilter(filter) {
            this.filter = filter;
            this.loaderEvent();
        },
        /*searchContent() {
            // always case insensitive
            const elements = this.$el.getElementsByClassName('box');
            const search = this.search.toLowerCase();
            for (let i = 0; i < elements.length - 1; i++) {
                const element = elements[i];
                const content = element.innerText.toLowerCase();

                element.style.display = content.includes(search)
                    ? 'block'
                    : 'none';
            }
        },*/

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
