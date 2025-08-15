<template>
    <modal
        id="enabling-objective-modal"
        name="enabling-objective-modal"
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
                        {{ trans('global.enablingObjective.create')  }}
                    </span>

                    <span v-if="method === 'patch'">
                        {{ trans('global.enablingObjective.edit')  }}
                    </span>
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
            <form>
            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.enablingObjective.fields.title') }} *</label>
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
                    <label for="description">{{ trans('global.enablingObjective.fields.description') }}</label>
                    <textarea
                    id="description"
                    name="description"
                    class="form-control description my-editor "
                    v-model="form.description"
                    ></textarea>
                    <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                </div>

                <div class="form-group ">
                    <label for="level_id">
                        {{ trans("global.objectiveType.title_singular") }}
                    </label>
                    <select name="level_id"
                            id="level_id"
                            class="form-control select2 "
                            style="width:100%;"
                            >
                        <option value="">-</option>
                        <option v-for="(item,index) in levels" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>
                <div class="form-group ">
                    <label for="time_approach">{{ trans('global.enablingObjective.fields.time_approach') }}</label>
                    <input
                        type="text" id="time_approach"
                        name="title"
                        class="form-control"
                        v-model="form.time_approach"
                        />
                    <p class="help-block" v-if="form.errors.time_approach" v-text="form.errors.time_approach[0]"></p>
                </div>
                <div class="form-group ">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="visibility" v-model="form.visibility" >
                        <label class="form-check-label" for="visibility">{{ trans('global.navigator_item.fields.visibility_show') }}</label>
                    </div>
                </div>
            </div>
                <div class="card-footer">
                    <span class="pull-right">
                         <button type="button" class="btn btn-info" data-widget="remove" @click="close()">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-primary" @click.prevent="submit()" >{{ trans('global.save') }}</button>
                    </span>
                </div>
            </form>
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';

    export default {
        data() {
            return {
                component_id: this._uid,
                value: null,
                levels: [],
                method: 'post',
                requestUrl: '/enablingObjectives',
                form: new Form({
                    'id': '',
                    'title': '',
                    'description': '',
                    'time_approach': '',
                    'curriculum_id': '',
                    'terminal_objective_id': '',
                    'level_id': null,
                    'visibility': true,
                }),
            }
        },

        methods: {
            onChange(value) {
                this.form.level_id = value.id;
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
                this.form.id = '';
                this.form.title = '';
                this.form.description = '';
                if (event.params.objective){
                    this.method = event.params.method;
                    this.form.populate( event.params.objective );
                    //set selected
                    if (this.form.level_id != null){
                        this.value = {
                            'id': this.form.level_id,
                            'title': this.findObjectByKey(this.levels, 'id', this.form.level_id).title
                        };
                    }
                }
            },
            opened(){
                this.$initTinyMCE([
                    "autolink link example"
                ], {
                    'public': 1,
                    'referenceable_type': 'App\\\Curriculum',
                    'referenceable_id': this.form.curriculum_id,
                    'eventHubCallbackFunction': 'insertContent',
                    'eventHubCallbackFunctionParams': this.component_id
                });
                this.initSelect2();
            },
            initSelect2(){
                $("#level_id").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.onChange(e.params.data);
                }.bind(this))  //make onChange accessible!
                .val(this.form.level_id).trigger('change'); //set value

            },
            beforeClose() {
                //console.log('close')
            },
            loadData: function () {
                axios.get('/levels').then(response => {
                    this.levels = response.data;
                }).catch(e => {
                    console.log(e);
                });
            },
            submit() {
                var method = this.method.toLowerCase();
                this.form.title = tinyMCE.get('title').getContent();
                this.form.description = tinyMCE.get('description').getContent();
                if (method === 'patch'){
                    this.form.patch(this.requestUrl + '/' + this.form.id)
                             .then(response => {
                                 this.$eventHub.$emit("addEnablingObjective", response.message);
                             });
                } else {
                    this.form.post(this.requestUrl)
                             .then(response => this.$eventHub.$emit('addEnablingObjective', response.message));
                }

                this.close();
            },
            close(){
                tinymce.remove()
                this.$modal.hide('enabling-objective-modal');
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
