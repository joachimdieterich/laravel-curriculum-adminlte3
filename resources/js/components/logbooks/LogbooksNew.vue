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
            <div v-for="(logbook, index) in logbooks" v-if="(logbook.title.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                || search.length < 3" :id="logbook.id" v-bind:value="logbook.id"
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

                    <div class="symbol" style="position: absolute;
                        padding: 6px;
                        z-index: 1;
                        width: 30px;
                        height: 40px;
                        background-color: #0583C9;
                        top: 0px;
                        font-size: 1.2em;
                        left: 10px;">
                        <i v-if="$userId == logbook.owner_id" class="fa fa-user text-white pt-2"></i>
                        <i v-else class="fa fa-share-nodes text-white pt-2"></i>
                    </div>
                    <span v-if="$userId == logbook.owner_id" class="p-1 pointer_hand" accesskey=""
                        style="position:absolute; top:0px; height: 30px; width:100%;">

                        <button :id="'delete-logbook-' + logbook.id" type="submit" class="btn btn-danger btn-sm pull-right"
                            @click.prevent="confirmItemDelete(logbook.id)">
                            <small><i class="fa fa-trash"></i></small>
                        </button>

                        <a :href="'/logbooks/' + logbook.id + '/edit'" class="btn btn-primary btn-sm pull-right mr-1">
                            <small><i class="fa fa-pencil-alt"></i></small>
                        </a>
                    </span>
                </a>

            </div>
        </div>

        <Modal :id="'logbookModal'" css="danger" :title="trans('global.logbook.delete')"
            :text="trans('global.logbook.delete_helper')" :ok_label="trans('global.logbook.delete')" v-on:ok="destroy()" />
    </div>
</template>

<script>
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
        loaderEvent() {
            axios.get('logbooks/list?filter=' + this.filter)
                .then(response => {
                    this.logbooks = response.data.data;
                })
                .catch(e => {
                    this.errors = e.data.errors;
                });
        },
        setFilter(filter) {
            this.filter = filter;
            this.loaderEvent();
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
            // always case insensitive
            const elements = this.$el.getElementsByClassName('box');
            const search = filter.toLowerCase();

            for (let i = 0; i < elements.length; i++) {
                const element = elements[i];
                const content = element.innerText.toLowerCase();
                
                element.style.display = content.includes(search)
                    ? 'block'
                    : 'none';
            }

        });
        this.$eventHub.$on('removeFilter', () => {
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
