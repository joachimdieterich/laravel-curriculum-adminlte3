<template>
    <modal 
        id="terminal-objective-modal" 
        name="terminal-objective-modal" 
        height="auto" 
        :adaptive=true
        :scrollable=true
        :draggable=true
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 25000">
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
                    <button type="button" class="btn btn-tool" data-widget="remove" @click="$emit('close')">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>
            <form >
                <div class="card-body">
                    <div class="form-group "
                         :class="form.errors.title ? 'has-error' : ''"
                         >
                        <label for="title">{{ trans('global.terminalObjective.fields.title') }} *</label>
                        <input 
                            type="text" id="title" 
                            name="title" 
                            class="form-control" 
                            v-model="form.title"
                            placeholder="Title" 
                            required
                            />
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>
                    <div class="form-group "
                         :class="form.errors.description ? 'has-error' : ''"
                         >
                        <label for="description">{{ trans('global.terminalObjective.fields.description') }}</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-control" 
                            v-model="form.description"
                            placeholder="Description" 
                            />
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


                </div>
                <div class="card-footer">
                    <div class="form-group m-2">
                        <button type="button" class="btn btn-info" data-widget="remove" @click="$emit('close')">{{ trans('global.cancel') }}</button>
                        <button class="btn btn-info" @click="submit()" >{{ trans('global.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';
    import Multiselect from 'vue-multiselect'

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
                }),
            }
        },

        components: {
            Multiselect
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
                    //console.log(event.params.objective);
                    this.form.populate(event.params.objective);
                }

                this.method = event.params.method;

                //set selected
                this.value = {
                    'id': this.form.objective_type_id,
                    'title': this.findObjectByKey(this.objectiveTypes, 'id', this.form.objective_type_id).title
                };
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

                if (method === 'patch') {
                    this.requestUrl += '/' + this.form.id;
                }

                this.form.submit(method, this.requestUrl);

                this.$parent.$emit('addTerminalObjective', this.form);

                this.$modal.hide('terminal-objective-modal');
                //todo .then .catch


            }
        },
        created() {

            this.loadData();
        },
        mounted() {
            //console.log('Component mounted.')
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>