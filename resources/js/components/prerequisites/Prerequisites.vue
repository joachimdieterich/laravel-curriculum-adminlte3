<template>

    <div >
        <section id="visualisation" ></section>
        <p v-for="item in this.prerequisites.children"
           v-can="'prerequisite_delete'">
            {{ item.name }}, {{ item.description }}
            <i class="fa fa-trash text-danger pull-right pointer"
               @click="destroyPrerequisite(item.prerequisite_id)"></i>
        </p>
    </div>

</template>
<script>
    var mitchTree = require('d3-mitch-tree');
    export default {
        name: 'prerequisites',
        props: {
            successor_type: String,
            successor_id: Number,
        },
        data() {
            return {
                prerequisites : {},

                errors: {},
            }
        },
        methods: {
            loaderEvent() {
                axios.get('/prerequisites?successor_type=' + this.successor_type + '&successor_id=' + this.successor_id)
                    .then(response => {
                        this.processResponse(response);
                    }).catch(e => {
                    this.errors = e.response.data.errors;
                });
            },
            destroyPrerequisite(id){
                axios.delete("/prerequisites/" + id )
                    .then(response => {
                        this.loaderEvent();
                    }).catch(e => {
                    this.errors = e.response.data.errors;
                });
            },
            processResponse(response){
                this.prerequisites = response.data.prerequisites;
                var treePlugin = new mitchTree.boxedTree()
                    .setData(this.prerequisites)
                    .setElement(document.getElementById("visualisation"))
                    .setIdAccessor(function(data) {
                        return data.id;
                    })
                    .setChildrenAccessor(function(data) {
                        return data.children;
                    })
                    .setBodyDisplayTextAccessor(function(data) {
                        return data.description;
                    })
                    .setTitleDisplayTextAccessor(function(data) {
                        return data.name;
                    })
                    .getNodeSettings()
                    .setSizingMode('nodesize')
                    .setVerticalSpacing(50)
                    .setHorizontalSpacing(50)
                    .back()
                    .initialize();

            },
            removeHtmlTags(string){
               return string.replace(/<[^>]+>/g, '');
            }

        },
        mounted() {


        }

    }
</script>


<style lang="scss">
    @import 'node_modules/d3-mitch-tree/dist/css/d3-mitch-tree.min';
    @import 'node_modules/d3-mitch-tree/dist/css/d3-mitch-tree-theme-default.min';
</style>
