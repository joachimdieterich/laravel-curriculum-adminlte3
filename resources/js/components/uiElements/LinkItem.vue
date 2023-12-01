<template>

    <a class="btn btn-block btn-outline-primary"
       :id="component_id"
       :href="href"
        style="max-width: 250px;vertical-align: middle !important;">
        <img
            class="bg-white rounded pull-left"
            :src="src"/>
        <b class="pull-left pl-2">Link Title</b>
    </a>

</template>

<script>
    export default {
        props: {
            href: '',
            defaultFavIcon: '/favicon.png',
        },
        data() {
            return {
                component_id: this._uid,
                src: '',
            }
        },
        methods: {
            faviconizeElements(){
                this.src = this.getDomainFaviconURL(this.href);
                //console.log(this.src);
            },
             getDomainFaviconURL(linkurl){
                let domain = linkurl.match(/(\w+):\/\/([^/:]+)(:\d*)?([^# ]*)/);
                domain = RegExp.$2;
                let faviconurl = "http://"+domain+"/favicon.ico";
                return faviconurl;
            }
        },
        mounted() {
            this.faviconizeElements();
        }

    }
</script>
