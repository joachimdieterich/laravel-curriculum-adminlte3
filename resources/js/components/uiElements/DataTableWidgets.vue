<template> 
    <div class="row">
        <div v-for="entry in entries.data"
             class="col-sm-6 col-md-6 col-lg-4" :id="entry.id">
            <a :href="'logbooks/'+entry.id"
                class="text-decoration-none text-black">
                <div class="info-box elevation-1">
                    <span class="info-box-icon bg-info elevation-1">
                        <i class="fa fa-book"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="pull-right" v-html="entry.action"></span>
                        <span class="info-box-text">{{ entry.title }}</span>
                        <span class="pt-2 info-box-number" v-html="decodeHtml(entry.description)"></span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>

<script> 
    export default {
        data () {
            return {
                entries: ''
            };
        }, 
        
        mounted() {
           axios.get('logbooks/list')
                .then(response => (this.entries = response.data))
                .catch(function (error) {
                // handle error
                console.log(error);
                });
          
        },
         methods: {
            decodeHtml(html) {
                var txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value;
            },
            
         }
    }
</script>