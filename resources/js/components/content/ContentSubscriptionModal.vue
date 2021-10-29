<template>
    <modal
        id="content-subscription-modal"
        name="content-subscription-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1200">
        <div class="card"
             style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ trans('global.content.copy') }}
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

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group "
                    :class="form.errors.title ? 'has-error' : ''"
                      >
                    <label for="title">{{ trans('global.curriculum.title_singular') }} *</label>
                    <select name="curricula[]"
                            id="curricula"
                            class="form-control select2 "
                            style="width:100%;"
                            v-model="curriculum_id">
                        <option v-for="(item,index) in curricula" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                     <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                </div>

                <div class="form-group ">
                    <label for="contents">
                        {{ trans('global.content.title_singular') }}
                    </label>
                    <select name="contents[]"
                            id="contents"
                            class="form-control select2 "
                            style="width:100%;"
                            multiple=true
                            v-model="form.content_id">
                         <option v-for="(item,index) in contents" v-bind:value="item.id">{{ item.title }}</option>
                    </select>
                </div>

            </div>
            <div class="card-footer">
                <span class="pull-right">
                     <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>

        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation'

    export default {
        props: {
            subscribable_type: '',
            subscribable_id: ''
        },
        data() {
            return {
                curricula: Object,
                curriculum_id: null,
                contents: {},

                form: new Form({
                    'content_id': [],
                    'subscribable_type': null,
                    'subscribable_id': null,
                }),
            }
        },
        methods: {
            async submit( ) {
                try {
                    this.form.content_id = $("#contents").val();
                    this.location = (await axios.post('/contentSubscriptions', this.form)).data.message;
                    this.$parent.$emit('addContent', this.form);
                    this.close();
                } catch(error) {
                    this.form.errors = error.response.data.form.errors;
                }
            },
            beforeOpen(event) {
                this.getCurricula();
            },
            async getCurricula() {
                try {
                    this.curricula = (await axios.get('/curricula/')).data.curricula;
                } catch(error) {
                    console.log('loading failed')
                }
            },

            opened(){
                this.initSelect2();
                this.form.subscribable_type = this.subscribable_type;
                this.form.subscribable_id   = this.subscribable_id;
            },
            initSelect2(){
                $("#curricula").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: true
                }).on('select2:select', function (e) {
                    this.getContent($("#curricula").val());
                }.bind(this));
                $("#contents").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: true
                });
            },

            beforeClose() {
                //console.log('close')
            },
            async getContent(id) {
                try {
                    this.contents = null;
                    this.contents = (await axios.get('/curricula/'+id)).data.contents;
                } catch(error) {
                    //console.log('loading failed')
                }
            },
            close(){
                this.$modal.hide('content-subscription-modal');
            }
        },
        mounted() {
        },
    }
</script>
