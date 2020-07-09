<template >
    <div sticky-container > 
        <ul v-sticky sticky-offset="{top: 70}" 
            v-bind:id="'content_'+category.id" 
            class="hidden-sm pull-right content-index-list">
            <li v-for="(item,index) in contents" 
                class="content-index-list-item ">
                <span @click="scrollTo('content_'+category.id+'_'+item.content.id)" 
                   v-html="item.content.title"></span>
            </li> 
        </ul>
        
        <div v-for="(item,index) in contents"
             class="content-group">
            <h5 v-bind:id="'content_'+category.id+'_'+item.content.id" 
                v-bind:name="'content_'+category.id+'_'+item.content.id" 
                ><span v-html="item.content.title"></span>
                <span class="pull-right text-gray">
                    <i :class="{ 'fa fa-angle-up': index !== 0 }" 
                        @click="scrollTo('content_'+category.id)"></i>
                </span>
            </h5>
            <div v-html="item.content.content"></div>  
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
        methods: {
            scrollTo(id) { 
                $('html, body').animate({scrollTop: $('#'+id).offset().top -70 }, 'fast');
            },
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
.top-sticky {
    width:200px !important;
    left:auto !important;
}

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