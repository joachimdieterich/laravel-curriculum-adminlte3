<template>
    <div class="col-12">
        <ul class="nav nav-tabs"
            role="tablist">
            <li class="nav-item">
                <a class="nav-link link-muted active"
                   id="admin-nav-tab"
                   data-toggle="pill"
                   href="#admin-tab"
                   role="tab"
                   aria-controls="admin-tab"
                   aria-selected="true">
                    <i class="fas fa-th"></i>
                </a>
            </li>
           <li class="nav-item">
                <a class="nav-link link-muted"
                   id="metadata-nav-tab"
                   data-toggle="pill"
                   href="#metadata-tab"
                   role="tab"
                   aria-controls="metadata-tab"
                   aria-selected="false"
                   @click="loaderEvent()">
                    <i class="fa fa-barcode pr-2"></i>{{trans('global.metadata.title')}}
                </a>
            </li>
        </ul>

        <div class="tab-content"
             id="custom-content-below-tabContent">
            <div class="tab-pane fade show active pt-2"
                 id="admin-tab"
                 role="tabpanel"
                 aria-labelledby="curriculum-nav-tab">
             admintab
            </div>
            <div class="tab-pane fade "
                 id="metadata-tab"
                 role="tab"
                 aria-labelledby="metadata-nav-tab">
               metadata
            </div>


        </div>
    </div>
</template>

<script>


    export default {
        props: {

        },
        data () {
            return {

            };
        },

        methods: {
            loaderEvent: function() {

            }

        },
        mounted() {

        },
        components: {

        }

    }
</script>
