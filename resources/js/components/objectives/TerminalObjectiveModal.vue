<template>
    <modal 
        id="terminal-objective-modal" 
        name="terminal-objective-modal" 
        height="auto" 
        :adaptive=true
        :scrollable=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card" style="margin-bottom: 0px !important">
            <div class="card-header">
                <h3 class="card-title">

                    <span v-if="method === 'post'">
                        {{ trans('global.create')  }} 
                    </span>

                    <span v-if="method === 'patch'">
                        {{ trans('global.update')  }} 
                    </span>

                    {{ trans('global.terminalObjective.title_singular') }}
                </h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>
            <form >
                <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="form-group "
                         :class="form.errors.title ? 'has-error' : ''"
                         >
                        <label for="title">{{ trans('global.terminalObjective.fields.title') }} *</label>
                        <textarea
                            id="title"
                            name="title"
                            class="form-control description my-editor "                  
                            v-model="form.title"
                        ></textarea>
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>
                    <div class="form-group "
                         :class="form.errors.description ? 'has-error' : ''"
                         >
                        <label for="description">{{ trans('global.terminalObjective.fields.description') }}</label>
                        <textarea
                            id="description"
                            name="description"
                            class="form-control description my-editor "                  
                            v-model="form.description"
                         ></textarea>
                        <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                    </div>

                    <div class="form-group" 
                         :class="form.errors.title ? 'has-error' : ''">
                        <label for="objective_type_id" >{{ trans("global.objectiveType.title_singular") }}</label>

                        <multiselect v-model="value" 
                                     :options="objectiveTypes" 
                                     :multiple="false" 
                                     :close-on-select="true" 
                                     :clear-on-select="false" 
                                     :preserve-search="true" 
                                     placeholder="Pick some" 
                                     label="title" 
                                     track-by="id" 
                                     :preselect-first="true"
                                     @input="onChange">
                            <template slot="selection" slot-scope="{ values, search, isOpen }">
                                <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">
                                    {{ value.length }} options selected
                                </span>
                            </template>
                        </multiselect>
                        <p class="help-block" v-if="form.errors.objective_type_id" v-text="form.errors.objective_type_id[0]"></p>   

                    </div>
                   <ColorPicker 
                       :color="form.color" 
                       v-model="form.color"></ColorPicker>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="visibility" v-model="form.visibility" >
                        <label class="form-check-label" for="visibility">{{ trans('global.navigator_item.fields.visibility_show') }}</label>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="pull-right">
                         <button type="button" class="btn btn-info" data-widget="remove" @click="close()">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                    </span>
                </div>
            </form>
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';
    import Multiselect from 'vue-multiselect';
    import ColorPicker from '../uiElements/ColorPicker';

    export default {
        data() {
            return {
                value: null,
                objectiveTypes: [],
                method: 'post',
                requestUrl: '/terminalObjectives',
                form: new Form({
                    'id': '',
                    'title': '',
                    'description': '',
                    'color': '#008000',
                    'time_approach': '',
                    'curriculum_id': '',
                    'objective_type_id': '',
                    'visibility': true,
                }),
                colors: {
                    hex: '#194d33',
                    hsl: { h: 150, s: 0.5, l: 0.2, a: 1 },
                    hsv: { h: 150, s: 0.66, v: 0.30, a: 1 },
                    rgba: { r: 25, g: 77, b: 51, a: 1 },
                    a: 1
                  }
            }
        },

        methods: {
            onChange(value) {
                this.form.objective_type_id = value.id
            },
            findObjectByKey(array, key, value) {
                for (var i = 0;
                i < array.length; i++) {
                    if (array[i][key] === value) {
                        return array[i];
                    }
                }
                return null;
            },

            beforeOpen(event) {
                if (event.params.objective) {
                    this.method = event.params.method;
                    this.form.populate(event.params.objective);
                    //set selected
                    this.value = {
                        'id': this.form.objective_type_id,
                        'title': this.findObjectByKey(this.objectiveTypes, 'id', this.form.objective_type_id).title
                    };
                }                
            },
            opened(){
                this.$initTinyMCE();
            },
            beforeClose() {
                //console.log('close')
            },

            loadData: function () {
                axios.get('/objectiveTypes').then(response => {
                    this.objectiveTypes = response.data;
                }).catch(e => {
                    this.form.errors = error.response.data.errors;
                });
            },

            submit() {
                var method = this.method.toLowerCase();
                this.form.title = tinyMCE.get('title').getContent();
                this.form.description = tinyMCE.get('description').getContent();
                
                if (method === 'patch') {
                    this.requestUrl += '/' + this.form.id;
                }

                this.form.submit(method, this.requestUrl);

                this.$parent.$emit('addTerminalObjective', this.form);

                this.$modal.hide('terminal-objective-modal');
                //todo .then .catch
            },
             close(){
                this.$modal.hide('terminal-objective-modal');
            }
        },
        created() {

            this.loadData();
        },
        mounted() {
            //console.log('Component mounted.')
        },
        components: {
            Multiselect,
            ColorPicker
        },
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>