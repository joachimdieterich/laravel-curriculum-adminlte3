<template>
    <div class="d-flex flex-column align-items-center justify-content-center p-2">
        <button
            id="upload-button"
            class="btn btn-lg m-1"
            style="color: #6c757d;"
            @click="openUploadWindow()"
        >
            <i class="fa fa-upload pr-1"></i>
            {{ trans('global.medium.upload_cloud') }}
        </button>
        <span>- {{ trans('global.or') }} -</span>
        <button
            id="cloud-button"
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
// variables need to be outside of the component
// else closing a window will throw a security error
let uploadWindow = null;
let cloudWindow = null;

export default {
    props: {
        model: {},
    },
    data() {
        return {
            component_id: this._uid,
            uploadURL: '',
            cloudURL: '',
        };
    },
    methods: {
        openUploadWindow() {
            // in local environments fetching the URLs might take some time
            // so we wait until the URL is fetched, to not open a blank window
            if (this.uploadURL === '') {
                document.getElementById('upload-button').disabled = true;
                let waiting = setInterval(() => {
                    if (this.uploadURL !== '') {
                        clearInterval(waiting);
                        document.getElementById('upload-button').disabled = false;
                        uploadWindow = window.open(this.uploadURL, 'edusharing_upload');
                    }
                }, 100);
            } else {
                // focus the window if already opened, instead of reloading the page
                if (uploadWindow && !uploadWindow.closed) uploadWindow.focus();
                else uploadWindow = window.open(this.uploadURL, 'edusharing_upload');
            }
        },
        openCloudWindow() {
            if (this.cloudURL === '') {
                document.getElementById('cloud-button').disabled = true;
                let waiting = setInterval(() => {
                    if (this.cloudURL !== '') {
                        clearInterval(waiting);
                        document.getElementById('cloud-button').disabled = false;
                        cloudWindow = window.open(this.cloudURL, 'edusharing_cloud');
                    }
                }, 100);
            } else {
                if (cloudWindow && !cloudWindow.closed) cloudWindow.focus();
                else cloudWindow = window.open(this.cloudURL, 'edusharing_cloud');
            }
        },
        receiveMessage(event) {
            if (event.data.event === 'APPLY_NODE') {
                setTimeout(() => { this.emitEvent(event.data.data); }, 250);
                // close the tabs to also show the loading indicator
                if (!uploadWindow.closed) uploadWindow.close();
                if (!cloudWindow.closed) cloudWindow.close();
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

        window.addEventListener("message", this.receiveMessage);
    },
    unmounted() {
        window.removeEventListener("message", this.receiveMessage);
        if (uploadWindow && !uploadWindow.closed) uploadWindow.close();
        if (cloudWindow && !cloudWindow.closed) cloudWindow.close();
    },
}
</script>
<style scoped>
.btn:hover { color: #007bff !important; }
</style>