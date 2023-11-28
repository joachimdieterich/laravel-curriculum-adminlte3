<template>
    <div class="row">
        <div class="col-md-12 py-2">
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
                        <i class="fa fa-user  pr-2"></i>Meine Logb&uuml;cher
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
            <LogbookIndexWidget
                v-for="(logbook, index) in logbooks"
                :key="index + '_logbook_' + logbook.id"
                :logbook="logbook"
                :search="search"
            />
            <LogbookIndexAddWidget v-can="'logbook_create'"/>
        </div>
        <Modal :id="'logbookModal'" css="danger" :title="trans('global.logbook.delete')"
               :text="trans('global.logbook.delete_helper')" :ok_label="trans('global.logbook.delete')" v-on:ok="destroy()" />
    </div>
</template>
<script>
import { nextTick } from 'vue';
import LogbookIndexAddWidget from './LogbookIndexAddWidget';
import LogbookIndexWidget from './LogbookIndexWidget';

const Modal =
    () => import('./../uiElements/Modal');
//import Modal from "./../uiElements/Modal";

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            logbooks: [],
            subscriptions: {},
            search: '',
            errors: {},
            tempId: Number,
            filter: 'all'
        }
    },
    methods: {
        confirmItemDelete(logbookId) {
            $('#logbookModal').modal('show');
            this.tempId = logbookId;
        },
        editLogbook(logbook){
            this.$eventHub.$emit('edit_logbook', logbook);
            // window.location = "/logbooks/" + id + "/edit";
        },
        loaderEvent() {
            axios.get('/logbooks/list?filter=' + this.filter)
                .then(async response => {
                    this.logbooks = response.data.data;

                    await nextTick();
                    if (this.searchh != '') this.searchContent();
                })
                .catch(e => {
                    this.errors = e.data.errors;
                });
        },
        setFilter(filter) {
            this.filter = filter;
            this.loaderEvent();
        },
        searchContent() {
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
        },
        async destroy() {
            try {
                this.logbooks = (await axios.delete('/logbooks/' + this.tempId)).data.data;
            } catch (error) {
                console.log(error);
            }
        },
    },
    mounted() {
        this.loaderEvent();
        this.$eventHub.$emit('showSearchbar');
        this.$eventHub.$on('filter', (filter) => {
            this.search = filter;
            this.searchContent();
        });
        this.$eventHub.$on('removeFilter', () => {
            this.search = '';
            this.$el.getElementsByClassName('box').forEach(element => {
                element.style.display = 'block';
            });
        });
        this.$eventHub.$on('logbook-updated', (logbook) => {
            const index = this.logbooks.findIndex(
                lb => lb.id === logbook.id
            );

            for (const [key, value] of Object.entries(logbook)) {
                this.logbooks[index][key] = value;
            }
        });
    },
    components: {
        Modal,
        LogbookIndexAddWidget,
        LogbookIndexWidget
    },
}
</script>
<style scoped>
.nav-link:hover {
    cursor: default;
    user-select: none;
}
.nav-item:hover .nav-link:not(.active) {
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer;
}
</style>
