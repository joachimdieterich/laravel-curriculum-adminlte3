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
            <div v-for="(logbook, index) in logbooks" :id="logbook.id" v-bind:value="logbook.id"
                class="box box-objective nav-item-box-image pointer my-1" style="min-width: 200px !important;"
                :style="'border-bottom: 5px solid ' + (logbook.color ?? '#2980B9')">
                <a :href="'/logbooks/' + logbook.id" class="text-decoration-none text-black">
                    <div v-if="logbook.medium_id" class="nav-item-box-image-size"
                        :style="{ 'background': 'url(/media/' + logbook.medium_id + '?model=logbook&model_id=' + logbook.id + ') top center no-repeat', 'background-size': 'cover', }">
                        <div class="nav-item-box-image-size" style="width: 100% !important;"
                            :style="{ backgroundColor: (logbook.color ?? '#2980B9') + ' !important', 'opacity': '0.5' }">
                        </div>
                    </div>
                    <div v-else class="nav-item-box-image-size text-center"
                        :style="{ backgroundColor: (logbook.color ?? '#2980B9') + ' !important' }">
                        <i class="fa fa-2x p-5 fa-book nav-item-text text-white"></i>
                    </div>

                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                        <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                            {{ logbook.title }}
                        </h1>
                        <p class="text-muted small" v-html="decodeHtml(logbook.description)">
                        </p>
                    </span>

                    <div class="symbol"
                         :style="'color:' + $textcolor(logbook.color) + '!important'"
                         style="position: absolute; width: 30px; height: 40px;"
                    >
                        <i v-if="$userId == logbook.owner_id"
                           class="fa fa-user pt-2"></i>
                        <i v-else
                           class="fa fa-share-nodes pt-2"></i>
                    </div>

                    <div v-if="$userId == logbook.owner_id"
                         class="btn btn-flat pull-right "
                         :id="'logbookDropdown_' + logbook.id"
                         style="position:absolute; top:0; right: 0; background-color: transparent;"
                         data-toggle="dropdown"
                         aria-expanded="false">
                        <i class="fas fa-ellipsis-v"
                           :style="'color:' + $textcolor(logbook.color)"></i>
                        <div class="dropdown-menu dropdown-menu-right"
                             x-placement="left-start">

                            <button :name="'logbookEdit_'+logbook.id"
                                    class="dropdown-item text-secondary"
                                    @click.prevent="editLogbook(logbook.id)">
                                <i class="fa fa-pencil-alt mr-2"></i>
                                {{ trans('global.logbook.edit') }}
                            </button>
                            <hr class="my-1">
                            <button :id="'delete-logbook-' + logbook.id"
                                    type="submit"
                                    class="dropdown-item py-1 text-red"
                                    @click.prevent="confirmItemDelete(logbook.id)">
                                <i class="fa fa-trash mr-2"></i>
                                {{ trans('global.logbook.delete') }}
                            </button>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <Modal :id="'logbookModal'" css="danger" :title="trans('global.logbook.delete')"
            :text="trans('global.logbook.delete_helper')" :ok_label="trans('global.logbook.delete')" v-on:ok="destroy()" />
    </div>
</template>

<script>
import { nextTick } from 'vue';

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
        editLogbook(id){
            window.location = "/logbooks/" + id + "/edit";
        },
        loaderEvent() {
            axios.get('logbooks/list?filter=' + this.filter)
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

            for (let i = 0; i < elements.length; i++) {
                const element = elements[i];
                const content = element.innerText.toLowerCase();
                
                element.style.display = content.includes(search)
                    ? 'block'
                    : 'none';
            }
        },
        decodeHtml(html) {
            let txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value.replace(/(<([^>]+)>)/ig, "");
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
    },
    components: {
        Modal
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
