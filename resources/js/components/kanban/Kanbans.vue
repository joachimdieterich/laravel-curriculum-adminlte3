<template>
    <div class="row">
        <div class="col-md-12 py-2">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link "
                       :class="filter === 'all' ? 'active' : ''"
                       id="kanban-filter-all"
                       @click="setFilter('all')"
                       data-toggle="pill"
                       role="tab"
                       >
                       <i class="fa fa-columns pr-2"></i>Alle Pinnwände
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'owner' ? 'active' : ''"
                       id="custom-filter-owner"
                       @click="setFilter('owner')"
                       data-toggle="pill"
                       role="tab"
                    >
                        <i class="fa fa-user  pr-2"></i>Meine Pinnwände
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_with_me' ? 'active' : ''"
                       id="custom-filter-shared-with-me"
                       @click="setFilter('shared_with_me')"
                       data-toggle="pill"
                       role="tab"
                       >
                        <i class="fa fa-paper-plane pr-2"></i>Für mich freigegeben
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       :class="filter === 'shared_by_me' ? 'active' : ''"
                       id="custom-tabs-shared-by-me"
                       @click="setFilter('shared_by_me')"
                       data-toggle="pill"
                       role="tab"
                       >
                        <i class="fa fa-share-nodes  pr-2"></i>Von mir freigegeben
                    </a>
                </li>

            </ul>
        </div>

        <table id="kanban-datatable" style="display: none;"></table>
        <div id="kanban-content">
            <div v-for="(kanban,index) in kanbans"
                 :id="'kanban-' + kanban.id"
                 class="box box-objective nav-item-box-image pointer my-1"
                 style="min-width: 200px !important;"
                 :style="'border-bottom: 5px solid ' + kanban.color">
                <a :href="'/kanbans/'+kanban.id"
                   class="text-decoration-none text-black"
                   >
                    <div v-if="kanban.medium_id"
                         class="nav-item-box-image-size"
                         :style="{'background': 'url(/media/' + kanban.medium_id + '?model=Kanban&model_id=' + kanban.id +') top center no-repeat', 'background-size': 'cover', }">
                         <div class="nav-item-box-image-size"
                              style="width: 100% !important;"
                              :style="{backgroundColor: kanban.color + ' !important',  'opacity': '0.5'}">
                         </div>
                    </div>
                    <div v-else
                         class="nav-item-box-image-size text-center"
                         :style="{backgroundColor: kanban.color + ' !important'}">
                        <i class="fa fa-2x p-5 fa-columns nav-item-text text-white"></i>
                    </div>
    
                    <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                       <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                           {{ kanban.title }}
                       </h1>
                       <p class="text-muted small"
                          v-html="decodeHtml(kanban.description)">
                       </p>
                    </span>
    
                    <div class="symbol"
                         style="position: absolute;
                                padding: 6px;
                                z-index: 1;
                                width: 30px;
                                height: 40px;
                                background-color: #0583C9;
                                top: 0px;
                                font-size: 1.2em;
                                left: 10px;">
                        <i v-if="$userId == kanban.owner_id"
                           class="fa fa-user text-white pt-2"></i>
                        <i v-else
                           class="fa fa-share-nodes text-white pt-2"></i>
                    </div>
                    <span v-if="$userId == kanban.owner_id"
                          class="p-1 pointer_hand"
                          accesskey="" style="position:absolute; top:0px; height: 30px; width:100%;">
    
                       <button
                           :id="'delete-kanban-'+kanban.id"
                           type="submit" class="btn btn-danger btn-sm pull-right"
                           @click.prevent="confirmItemDelete(kanban.id)">
                           <small><i class="fa fa-trash"></i></small>
                       </button>
    
                       <a :href="'/kanbans/' + kanban.id + '/edit'"
                          class="btn btn-primary btn-sm pull-right mr-1">
                           <small><i class="fa fa-pencil-alt"></i></small>
                       </a>
                   </span>
                </a>
    
            </div>
        </div>
        
        <Modal
            :id="'kanbanModal'"
            css="danger"
            :title="trans('global.kanban.delete')"
            :text="trans('global.kanban.delete_helper')"
            :ok_label="trans('global.kanban.delete')"
            v-on:ok="destroy()"
        />
    </div>
</template>

<script>
const Modal =
    () => import('./../uiElements/Modal');
//import Modal from "./../uiElements/Modal";
let observer;
const observerOptions = { childList: true, subtree: true };

export default {
    props: {
        subscribable_type: '',
        subscribable_id: '',
    },
    data() {
        return {
            kanbans: [],
            subscriptions: {},
            // search: '',
            url: 'kanbans/list',
            errors: {},
            tempId: Number,
            filter: 'all'
        }
    },
    methods: {
        confirmItemDelete(kanbanId){
            $('#kanbanModal').modal('show');
            this.tempId = kanbanId;
        },
        loaderEvent(){
            if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
                this.url = '/kanbanSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id
            }
            // axios.get(this.url + '?filter=' + this.filter)
            //     .then(async response => {
            //         if (typeof (this.subscribable_type) !== 'undefined' && typeof(this.subscribable_id) !== 'undefined'){
            //             this.kanbans = response.data.data;
            //         } else {
            //             this.kanbans = response.data.data;
            //         }

            //     })
            //     .catch(e => {
                //         console.log(e.data.errors);
                //     });
                
                $('#kanban-datatable').DataTable().ajax.url(this.url + '?filter=' + this.filter).load();
                // await nextTick();
                // if the searchbar had some input before changing filters, redo the search
                // if (this.search != '') this.searchContent();
        },
        setFilter(filter){
            this.filter = filter;
            this.loaderEvent();
        },
        // searchContent() {
            // always case insensitive
            // const elements = this.$el.getElementsByClassName('box');
            // const search = this.search.toLowerCase();

            // for (let i = 0; i < elements.length; i++) {
            //     const element = elements[i];
            //     const content = element.innerText.toLowerCase();
                
            //     element.style.display = content.includes(search)
            //         ? 'block'
            //         : 'none';
            // }
        // },
        decodeHtml(html) {
            let txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value.replace(/(<([^>]+)>)/ig,"");
        },
        async destroy() {
            try {
                this.kanbans = (await axios.delete('/kanbans/' + this.tempId)).data.data;
            } catch (error) {
                console.log(error);
            }
        },
        tableToData() { 
            const table = document.getElementById('kanban-datatable');

            if (table.getElementsByClassName('dataTables_empty').length > 0) {
                this.kanbans = [];
                return;
            }

            const headers = table.querySelector('thead tr').children;
            let data = [];

            for (let row = 1; row < table.rows.length; row++) { 
                let tableRow = table.rows[row]; 
                let rowData = {}; 
                for (let cell = 0; cell < tableRow.cells.length; cell++) { 
                    rowData[headers[cell].innerText] = tableRow.cells[cell].innerHTML;
                } 
                data.push(rowData); 
            } 
            this.kanbans = data; 
        }
    },
    mounted() {
        // this.loaderEvent();

        this.$eventHub.$on('filter', (filter) => {
            // this.search = filter;
            // this.searchContent();
            $('#kanban-datatable').DataTable().search(filter).draw();
        });
        this.$eventHub.$on('removeFilter', () => {
            // this.search = '';
            // this.$el.getElementsByClassName('box').forEach(element => {
            //     element.style.display = 'block';
            // });
            $('#kanban-datatable').DataTable().search('').draw();
        });
        this.$eventHub.$emit('showSearchbar');

        // checks if the datatable-data changes, to update the kanban-data
        observer = new MutationObserver(this.tableToData);
        observer.observe(document.getElementById('kanban-datatable'), observerOptions);

        $('#kanban-datatable').DataTable({
            ajax: this.url + '?filter=' + this.filter,
            columns: [
                { title: 'color', data: 'color' },
                { title: 'description', data: 'description' },
                { title: 'id', data: 'id' },
                { title: 'medium_id', data: 'medium_id' },
                { title: 'owner_id', data: 'owner_id' },
                { title: 'title', data: 'title' },
            ],
            pageLength: 6,
        });

        // place the content where the table would normally be
        $('#kanban-content').insertBefore('#kanban-datatable');
    },
    components: {
        Modal
    },
}
</script>
<style>
/* hide the built-in datatable-searchbar, but keep its functionality */
.dataTables_filter { display: none; }
</style>
