<template>
    <div class="col-12">
        <ul class="nav nav-tabs"
            role="tablist">
            <li class="nav-item">
                <a class="nav-link link-muted active"
                   id="curriculum-nav-tab"
                   data-toggle="pill"
                   href="#curriculum-tab"
                   role="tab"
                   aria-controls="curriculum-tab"
                   aria-selected="false">
                    <i class="fas fa-th"></i>
                </a>
            </li>
           <!-- <li class="nav-item">
                <a class="nav-link link-muted"
                   id="content-nav-tab"
                   data-toggle="pill"
                   href="#content-tab"
                   role="tab"
                   aria-controls="content-tab"
                   aria-selected="true"
                   @click="loaderEvent()">
                    <i class="fa fa-align-justify pr-2"></i>{{trans('global.content.title')}}
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link link-muted"
                   id="medium-nav-tab"
                   data-toggle="pill"
                   href="#medium-tab"
                   role="tab"
                   aria-controls="medium-tab"
                   aria-selected="true">
                    <i class="fa fa-folder-open pr-2"></i>{{trans('global.media.title')}}
                </a>
            </li>
            <li class="nav-item ">
                <a v-if="group.glossar != null"
                   class="nav-link link-muted"
                   id="glossar-nav-tab"
                   data-toggle="pill"
                   href="#glossar-tab"
                   role="tab"
                   aria-controls="glossar-tab"
                   aria-selected="true">
                    <i class="fa fa-book-open pr-2"></i>{{trans('global.glossar.title_singular')}}
                </a>
                <a v-else
                   v-can="'glossar_create'"
                   class="nav-link link-muted"
                   id="glossar-nav-tab"
                   :href="'/glossar/create?subscribable_type=App\\Group&subscribable_id='+ group.id "
                 >
                    <i class="fa fa-book-open pr-2"></i>{{trans('global.glossar.create')}}
                </a>
            </li>-->
           <li class="nav-item "
               v-can="'logbook_access'">
                <a v-if="logbooks"
                   class="nav-link link-muted"
                   :href="'/logbooks/'+ logbooks[0].id "
                   id="logbook-nav-tab">
                    <i class="fas fa-book pr-2"></i>{{trans('global.logbook.title_singular')}}
                </a>
                <a v-else
                   v-can="'logbook_create'"
                   class="nav-link link-muted"
                   :href="'/logbooks/create?subscribable_type=App\\Group&subscribable_id='+ group.id "
                   id="logbook-nav-tab">
                    <i class="fas fa-book pr-2"></i>{{trans('global.logbook.create')}}
                </a>
            </li>

            <li v-can="'group_edit'"
                class="nav-item ml-auto">
                <a class="nav-link link-muted"
                   :href="'/groups/'+ group.id +'/edit'"
                   id="config-nav-tab">
                    <i class="fa fa-cog"></i>
                </a>
            </li>
        </ul>

        <div class="tab-content"
             id="custom-content-below-tabContent">
            <div class="tab-pane fade show active pt-2"
                 id="curriculum-tab"
                 role="tabpanel"
                 aria-labelledby="curriculum-nav-tab">
                <course-item
                    v-for="(item,index) in courses"
                    :key="'course_item'+index"
                    :course="item">
                </course-item>
            </div>
            <!--<div class="tab-pane fade "
                 id="content-tab"
                 role="tab"
                 aria-labelledby="content-nav-tab">
                <contents
                 ref="Contents"
                 subscribable_type="App\Group"
                :subscribable_id="group.id"></contents>
            </div>
            <div class="tab-pane fade "
                 id="medium-tab"
                 role="tab"
                 aria-labelledby="medium-nav-tab">
                <media subscribable_type="App\Group"
                       :subscribable_id="group.id"
                       format="list">
                </media>
            </div>-->
            <!--<div v-if="group.glossar != null"
                class="tab-pane fade"
                 id="glossar-tab"
                 role="tab"
                 aria-labelledby="glossar-nav-tab">
                <glossars
                    :glossar="group.glossar">
                </glossars>
            </div>-->

        </div>
    </div>
</template>

<script>
    import CourseItem from '../course/CourseItem.vue'
    import Glossars from '../glossar/Glossars';
    import Media from '../media/Media';
    import Contents from '../content/Contents';

    export default {
        props: {
            'group': Array,
            'courses': Array,
            'logbooks': Array,
        },
        data () {
            return {

            };
        },

        methods: {
            loaderEvent: function() {
                this.$refs.Contents.loaderEvent();
            }

        },
        mounted() {

        },
        components: {
            CourseItem,
            Media,
            Glossars,
            Contents
        }

    }
</script>
