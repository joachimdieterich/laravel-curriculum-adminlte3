<template>
    <div>
        <ul class="nav nav-pills row pb-2">
            <li class="nav-item small col-6">
                <a class="nav-link show active"
                   href="#edusharing_new"
                   data-toggle="tab">
                <i class="fa fa-upload pr-1"></i>
                    Medien (in die Cloud) hochladen
                </a>
            </li>

            <li class="nav-item small col-6">
                <a class="nav-link show"
                   href="#edusharing_link"
                   data-toggle="tab">
                <i class="fa fa-add pr-1"></i>
                    Medien aus der Cloud verknüpfen
                </a>
            </li>
        </ul>

        <div class="tab-content row">
            <div id="edusharing_new"
                 class="tab-pane col-12 active">
                <iframe
                    id="eduSharingNewFrame"
                    :src="this.uploadIframeUrl"
                    :width="this.width"
                    :height="this.height"
                    style="height: 80vh;"
                    frameborder="0"
                >
                </iframe>
            </div>
            <div id="edusharing_link"
                 class="tab-pane col-12">
                <iframe
                    id="eduSharingLinkFrame"
                    :src="this.cloudIframeUrl"
                    :width="this.width"
                    :height="this.height"
                    style="height: 80vh;"
                    frameborder="0"
                >
                </iframe>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        'model': {}
    },
    data() {
        return {
            component_id: this._uid,
            width:          "100%",
            height:         "650",
            uploadIframeUrl: '',
            cloudIframeUrl:  '',
        };
    },
    methods: {
        receiveMessage(event) {
            let data = event.data.data;

            if(event.data.event === 'APPLY_NODE') {
                //console.log(data);
                setTimeout(() => {  this.emitEvent(data); }, 2000); //

                window.removeEventListener("message", this.receiveMessage);
            }

        },
        emitEvent(data){
            this.$eventHub.$emit('external_add', {
                path:               data.content.url,
                thumb_path:         data.preview.url,
                medium_name:        data.name,
                title:              data.title,
                author:             this.getAuthor(data.owner),
                size:               data.size,
                mimetype:           data.mimetype,
                license_id:         this.getLicenseID(data.license.icon),
                external_id:        data.nodeId, //= data.ref.id,
                subscribable_id:    this.model.subscribable_id,
                subscribable_type:  this.model.subscribable_type,
                repository:         'edusharing',
                public:             1,
            });

        },
        getAuthor(owner){
          let author = '';
          if (typeof owner !== 'undefined'){
              author = owner?.firstName + ' ' + owner?.lastName;
          }
          return author;
        },
        getLicenseID(licenseURL) {
            if (licenseURL.search(/none.svg/i) !== -1){
                return 1;
            } else if (licenseURL.search(/copyright-license.svg/i) !== -1){
                return 2;
            } else if ((licenseURL.search(/cc-0.svg/i) !== -1) && (licenseURL.search(/pdm.svg/i) !== -1)){
                return 3;
            } else if (licenseURL.search(/cc-by.svg/i) !== -1){
                return 4;
            } else if (licenseURL.search(/cc-by-nd.svg/i) !== -1){
                return 5;
            } else if (licenseURL.search(/cc-by-nc-nd.svg/i) !== -1){
                return 6;
            } else if (licenseURL.search(/cc-by-nc.svg/i) !== -1){
                return 7;
            } else if (licenseURL.search(/cc-by-nc-sa.svg/i) !== -1){
                return 8;
            } else if (licenseURL.search(/cc-by-sa.svg/i) !== -1){
                return 9;
            } else {
                return 1;
            }
        }
    },

    mounted() {
        axios.get('/media/create?repository=edusharing')
            .then(response => {
                this.uploadIframeUrl = response.data.uploadIframeUrl;
                this.cloudIframeUrl  = response.data.cloudIframeUrl;
            })
            .catch(e => {
                console.log(e);
            });

        window.addEventListener("message", this.receiveMessage, false);
    },

}
</script>
