<template >
    <div class="row ">
        <div class="col-12 pt-2">
            <div class="card">
                <div v-if="create">
                    <div class="card-header pointer"
                         @click="edit()">
                        <i class="fas fa-add pr-1"></i>
                        {{ trans('global.planEntry.create') }}
                    </div>
                </div>
                <div v-else>
                    <div :id="'plan-entry-' + entry.id"
                         v-if="!editor"
                         :style="{ 'border-left-style': 'solid', 'border-radius': '0.25rem', 'border-color': entry.color }">
                        <div class="card-header collapsed" data-toggle="collapse" :data-target="'#plan-entry-' + entry.id + ' > .card-body'" aria-expanded="false">
                            <i class="mr-1"
                            :class="entry.css_icon"></i>
                            {{ entry.title }}
                            <i class="fa fa-angle-up"></i>
                            <div v-if="$userId == plan.owner_id"
                                class="card-tools">
                                <i class="fa fa-pencil-alt mr-2 pointer link-muted"
                                   @click="edit()"></i>
                                <i class="fas fa-trash pointer text-danger"
                                   @click="destroy(entry)"></i>
                            </div>
                        </div>
                        <div class="card-body py-2 collapse">
                            <img v-if="Number.isInteger(entry.medium_id)"
                                 class="pull-right"
                                 :src="'/media/' + entry.medium_id + '/thumb'"/>
                            <span v-dompurify-html="entry.description"></span>

                            <objectives
                                referenceable_type="App\PlanEntry"
                                :referenceable_id="entry.id"
                                :owner_id="entry.owner_id"
                                :editable="editable"
                            ></objectives>

                            <Trainings
                                :plan="plan"
                                subscribable_type="App\PlanEntry"
                                :subscribable_id="entry.id"
                            ></Trainings>
                        </div>
                    </div>
                </div>
                <div v-if="editor"
                     class="card-body">
                    <v-swatches
                        :swatch-size="49"
                        :trigger-style="{}"
                        popover-to="right"
                        v-model="this.form.color"

                        show-fallback
                        fallback-input-type="color"

                        @input="(id) => {
                                    if(id.isInteger){
                                      this.form.color = id;
                                    }

                                }"
                        :max-height="300"
                    ></v-swatches>

                    <div class="form-group">
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            v-model.trim="form.title"
                            :placeholder="trans('global.planEntry.fields.title')"
                            required
                        />
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>

                    <div class="form-group">
                        <Editor
                            id="description"
                            name="description"
                            :placeholder="trans('global.planEntry.fields.description')"
                            class="form-control description my-editor"
                            :init="tinyMCE"
                            :initial-value="form.description"
                        ></Editor>
                        <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                    </div>
                    <div class="form-group">
                        <font-awesome-picker
                            :searchbox="trans('global.select_icon')"
                            v-on:selectIcon="setIcon"
                        ></font-awesome-picker>
                    </div>

                    <div class="form-group">
<!--                        <MediumForm :form="form"
                                    :id="component_id"
                                    :medium_id="form.medium_id"
                                    accept="image/*"/>-->
                    </div>
                    <button :name="'planEntrySave'"
                            class="btn btn-primary p-2 m-2"
                            @click="submit()">
                        {{ trans('global.save') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import Calendar from '../calendar/Calendar.vue';
import Editor from '@tinymce/tinymce-vue';
import Form from "form-backend-validation";
import MediumForm from "../media/MediumForm.vue";
import Objectives from "../objectives/Objectives.vue";
import FontAwesomePicker from "../../../views/forms/input/FontAwesomePicker.vue";

import Trainings from '../training/Trainings.vue';

export default {
    props: {
        entry: {
            default: null
        },
        create: {
            default: false
        },
        plan: {
            type: Object
        },
        editable: {
            default: false
        },
    },
    data() {
        return {
            component_id: this.$.uid,
            method: 'post',
            requestUrl: '/planEntries',
            form: new Form({
                'id': null,
                'title':'',
                'description': '',
                'plan_id': '',
                'css_icon': 'fas fa-calendar-day',
                'order_id': 0,
                'color': '#27AF60',
                'medium_id': null,

            }),
            editor: false,
            errors: {},
            owner_id: ''
        }
    },
    mounted() {
        this.owner_id = this.plan.owner_id;

        if ( this.entry !== null ) {
            this.form.id = this.entry.id;
            this.form.title = this.entry.title;
            this.form.description = this.entry.description;
            this.form.medium_id = this.entry.medium_id;
            this.form.css_icon = this.entry.css_icon;
            this.form.order_id = this.entry.order_id;

            this.method = 'patch';
        }
        this.form.plan_id = this.plan.id;

        this.$initTinyMCE([
            "autolink link"
        ] );

        // Set eventlistener for Media
        this.$eventHub.on('addMedia', (e) => {
            if (this.component_id == e.id) {
                this.form.medium_id = e.selectedMediumId;
                if ( Array.isArray(this.form.medium_id))  {
                    this.form.medium_id = this.form.medium_id[0]; //Hack to get existing files working.
                }
            }
        });
    },
    methods: {
        setIcon(selectedIcon) {
            this.form.css_icon = 'fa fa-'+  selectedIcon.className;
        },
        edit() {
            this.editor = !this.editor ;
            if ( this.entry !== null ) {
                this.form.color = this.entry.color;
                this.form.css_icon = this.entry.css_icon;
                this.form.order_id = this.plan.entries?.length ;
            }

            this.$nextTick(() => {
                this.$initTinyMCE( );
            });
        },
        destroy(entry){
            axios.delete('/planEntries/'+entry.id)
                .then(response => {
                    this.$eventHub.emit("plan_entry_deleted", entry);
                })
                .catch(e => {
                    console.log(e);
                });
        },
        submit() {
            let method = this.method.toLowerCase();
            this.form.description = tinyMCE.get('description').getContent();
            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a task
                        this.$eventHub.emit("plan_entry_updated", res.data.entry);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => { // Tell the parent component we've added a new task and include it
                        this.$eventHub.emit("plan_entry_added", res.data.entry);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            }
            this.editor = false;

        },
    },

    components: {
        Editor,
        Calendar,
        FontAwesomePicker,
        Objectives,
        MediumForm,
        Trainings,
    },
}
</script>
<style scoped>
.card-header:hover {
    background-color: #e9ecef;
    cursor: pointer;
}
.card-header .fa-angle-up {
    transition: 0.3s transform;
}
.card-header.collapsed .fa-angle-up {
    transform: rotate(-180deg);
}
</style>
