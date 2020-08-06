<template> 
    <div class="row">
        <div v-for="entry in entries.data"
             class="col-sm-6 col-md-6 col-lg-4" :id="entry.id">
            <span  v-if="entry.medium_id != null" >
                <a :href="modelUrl+'/'+entry.id"
                class="text-decoration-none text-black">
                    <div class="info-box elevation-1" :style="styles">
                        <div class="info-box-content">
                            <span class="pull-right bg-gray-light" v-html="entry.action"></span>
                            <span class="text-white">{{ entry.title }}</span><br>
                            <span class="pt-2 text-white small" v-html="decodeHtml(entry.description)"></span>
                        </div>
                    </div>
                </a>
            </span>
            <span v-else>
                <a :href="modelUrl+'/'+entry.id"
                    class="text-decoration-none text-black">
                    <div class="info-box elevation-1">      
                        <span  class="info-box-icon bg-info elevation-1">
                            <i v-if="modelUrl == 'logbooks'" class="fa fa-book"></i>
                            <i v-if="modelUrl == 'kanbans'" class="fa fa-columns"></i>
                        </span>
                            

                        <div class="info-box-content">
                            <span class="pull-right" v-html="entry.action"></span>
                            <span class="info-box-text">{{ entry.title }}</span>
                            <span class="pt-2 info-box-number" v-html="decodeHtml(entry.description)"></span>
                        </div>
                    </div>
                </a>
            </span>
        </div>
    </div>
</template>

<script> 
    export default {
        props: ['modelUrl'],
        
        data () {
            return {
                entries: '',
            };
        }, 
        
        mounted() {
            axios.get(this.modelUrl+'/list')
                .then(response => (this.entries = response.data))
                .catch(function (error) {
                // handle error
               // console.log(error);
                });  
        },
         methods: {
            decodeHtml(html) {
                var txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value.replace(/<[^>]+>/g, '');
            },
            
         },
         computed: {
                styles() {
                    return {
                        
                        'background-repeat': 'no-repeat',
                        'background': 'linear-gradient(rgba(0, 0, 0, 0.5),rgba(0, 0, 0, 0.5)),'+`url('/media/1375') `,
                    }
                }
            }
    }
</script>