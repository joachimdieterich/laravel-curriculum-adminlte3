<template >
    <div sticky-container :key="componentKey"   >
        <ul v-sticky sticky-offset="{top: 70}"
            v-bind:id="'content_'+category.id"
            class="hidden-sm pull-right content-index-list">
            <li v-for="(item,index) in contents"
                class="content-index-list-item ">
                <span @click="scrollTo('content_'+category.id+'_'+item.content.id)"
                   v-dompurify-html="item.content.title"></span>
            </li>
        </ul>

        <div v-for="(item,index) in contents"
             :id="'content_id_'+item.content.id"
             class="content-group">
            <h5 v-bind:id="'content_'+category.id+'_'+item.content.id"
                v-bind:name="'content_'+category.id+'_'+item.content.id"
                ><span v-dompurify-html="item.content.title"></span>
                <span class="pull-right text-gray">
                    <i :class="{ 'fa fa-angle-up': index !== 0 }"
                        @click="scrollTo('content_'+category.id)"></i>
                </span>
                <span class="pull-right text-gray mr-2">
                    <small><i class="fa fa-pencil-alt"
                        @click="edit(item)"></i></small>
                </span>
                <span class="pull-right text-danger mr-4">
                    <small><i class="fa fa-trash"
                        @click="destroy(item)"></i></small>
                </span>
            </h5>
            <div v-dompurify-html="item.content.content"></div>
            <hr>
        </div>
    </div>

</template>

<script>

    export default {
        props: {
            category: {},
            contents: {}
        },
        data() {
            return {
              componentKey: 0,
            };
        },
        methods: {
            scrollTo(id) {
                $('html, body').animate({scrollTop: $('#'+id).offset().top -70 }, 'fast');
            },
            edit(content){
                this.$modal.show('content-create-modal', {'method': 'patch', 'id': content.content_id, 'referenceable_type': content.subscribable_type, 'referenceable_id': content.subscribable_id, 'categorie_ids': content.content.categories.map(category => category.id)});
            },
            destroy(content){
                axios.delete("/contents/"+content.content_id, {
                            data: { referenceable_type: content.subscribable_type, referenceable_id: content.subscribable_id }
                        })
                        .then(res => { // Tell the parent component we've added a new task and include it
                            let index = this.contents.indexOf(content);
                            this.contents.splice(index, 1);
                            this.forceRerender();
                        })
                        .catch(err => {
                            console.log(err.response);
                         });
            },
            forceRerender() {
                this.componentKey += 1;
            }
        },
        mounted(){

        }
    }
</script>

<style>
.vue-sticky-placeholder{
    padding-top:0px !important;
}

.content-index-list {
    position: sticky !important ;
    right:20px;
    width:200px !important;
    padding-left:25px;
}
/*.top-sticky {
    width:200px !important;
    left:auto !important;
}*/

.content-index-list-item {
    display: flex;
    list-style: none;
    border:0px;
    border-bottom-width: 1px;
    border-style: dashed;
    border-color: #dae1e7;
    line-height: 1.5;
    padding: .75rem;
}

@media (min-width: 576px){
    .content-group {
        margin-right:200px;
    }
}
</style>
