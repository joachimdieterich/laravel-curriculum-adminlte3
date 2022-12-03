<template>

    <div >
<!--        <section id="visualisation" ></section>-->
        <ul>
            <li v-for="item in this.prerequisites.children"
               v-can="'prerequisite_delete'">
                {{ item.name }}<br><small>{{ item.description }}</small>

                <i class="fa fa-trash text-danger pull-right pointer"
                   @click="destroyPrerequisite(item.prerequisite_id)"></i>
            </li>
        </ul>

    </div>

</template>
<script>

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
            },
            removeHtmlTags(string){
               return string.replace(/<[^>]+>/g, '');
            }
        },
        mounted() {
        }

    }
</script>
