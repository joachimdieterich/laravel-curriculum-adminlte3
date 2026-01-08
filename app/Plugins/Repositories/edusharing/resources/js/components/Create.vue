<template>
    <div class="d-flex flex-column align-items-center justify-content-center p-2">
        <button
            class="btn btn-lg m-1"
            style="color: #6c757d;"
            @click="openUploadWindow()"
        >
            <i class="fa fa-upload pr-1"></i>
            {{ trans('global.medium.upload_cloud') }}
        </button>
        <span>- {{ trans('global.or') }} -</span>
        <button
            class="btn btn-lg m-1"
            style="color: #6c757d;"
            @click="openCloudWindow()"
        >
            <i class="fa fa-add pr-1"></i>
            {{ trans('global.medium.connect_cloud') }}
        </button>
    </div>
</template>
<script>
export default {
    props: {
        model: {},
    },
    data() {
        return {
            component_id: this._uid,
            uploadURL: '',
            cloudURL: '',
            uploadWindow: null,
            cloudWindow: null,
        };
    },
    methods: {
        openUploadWindow() {
            if (this.uploadWindow && !this.uploadWindow.closed) {
                this.uploadWindow.focus();
                return;
            }
            this.uploadWindow = window.open(this.uploadURL, 'edusharing_upload');
        },
        openCloudWindow() {
            if (this.cloudWindow && !this.cloudWindow.closed) {
                this.cloudWindow.focus();
                return;
            }
            window.open(this.cloudURL, 'edusharing_cloud');
        },
        receiveMessage(event) {
            let data = event.data.data;

            if (event.data.event === 'APPLY_NODE') {
                setTimeout(() => { this.emitEvent(data); }, 250);

                window.removeEventListener("message", this.receiveMessage);
            }
        },
        emitEvent(data) {
            this.$eventHub.emit('external_add', {
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
        getAuthor(owner) {
            let author = '';
            if (typeof owner !== 'undefined') {
                author = owner?.firstName + ' ' + owner?.lastName;
            }
            return author;
        },
        getLicenseID(licenseURL) {
            if (licenseURL.search(/none.svg/i) !== -1) {
                return 1;
            } else if (licenseURL.search(/copyright-license.svg/i) !== -1) {
                return 2;
            } else if ((licenseURL.search(/cc-0.svg/i) !== -1) && (licenseURL.search(/pdm.svg/i) !== -1)) {
                return 3;
            } else if (licenseURL.search(/cc-by.svg/i) !== -1) {
                return 4;
            } else if (licenseURL.search(/cc-by-nd.svg/i) !== -1) {
                return 5;
            } else if (licenseURL.search(/cc-by-nc-nd.svg/i) !== -1) {
                return 6;
            } else if (licenseURL.search(/cc-by-nc.svg/i) !== -1) {
                return 7;
            } else if (licenseURL.search(/cc-by-nc-sa.svg/i) !== -1) {
                return 8;
            } else if (licenseURL.search(/cc-by-sa.svg/i) !== -1) {
                return 9;
            } else {
                return 1;
            }
        },
    },
    mounted() {
        axios.get('/media/create?repository=edusharing')
            .then(response => {
                this.uploadURL = response.data.uploadIframeUrl;
                this.cloudURL  = response.data.cloudIframeUrl;
            })
            .catch(e => {
                console.log(e);
            });

        window.addEventListener("message", this.receiveMessage, false);
    },
    unmounted() {
        window.removeEventListener("message", this.receiveMessage);
        if (this.uploadWindow && !this.uploadWindow.closed) this.uploadWindow.close();
        if (this.cloudWindow && !this.cloudWindow.closed) this.cloudWindow.close();
    },
}
</script>
<style scoped>
.btn:hover { color: #007bff !important; }
</style>