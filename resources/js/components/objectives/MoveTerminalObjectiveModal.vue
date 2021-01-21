<template>
    <modal
        id="move-terminal-objective-modal"
        name="move-terminal-objective-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card"
             style="margin-bottom: 0 !important">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.terminalObjective.move_to_curriculum') }}
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
                <label for="curricula">
                    {{ trans('global.curriculum.title_singular') }}
                </label>
                <select name="curricula"
                        id="curricula"
                        class="form-control select2 "
                        style="width:100%;"
                >
                    <option v-for="item in curricula" v-bind:value="item.id">{{ item.title }}</option>
                </select>
            </div>

            <div class="card-footer" style="margin-top: 250px">
                <span class="pull-right">
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    import Form from 'form-backend-validation';
    export default {
        data() {
            return {
                method: 'patch',
                form: new Form({
                    'id': null,
                    'curriculum_id': null,
                    'objective_type_id': null,
                }),

                curricula: {},
                curriculum: {},
                referenceRequestUrl: null,
            }
        },
        methods: {

            async loadCurricula() {
                try {
                    this.curricula = (await axios.get('/curricula')).data.curricula;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
            },

              submit() {
                 this.form.submit('patch', '/terminalObjectives/' + this.form.id)
                     .then(response => {
                         window.location = response.message;
                     })
                     .catch(response => function () {
                         if (response.errors)
                         {
                             alert(response.errors);
                         }
                     });
               /* try {
                    if (this.method === 'patch'){
                        this.location = (await axios.patch('/terminalObjectives/' + this.form.id , {
                            'curriculum_id' : this.form.curriculum_id
                        })).data.message;
                    }
                    location.reload(true);
                } catch(error) {
                    //
                }*/
            },

            beforeOpen(event) {
                this.loadCurricula();
                if (event.params.objective){
                    this.form.populate( event.params.objective );
                }
            },
            beforeClose() {
            },
            opened(){
                this.initSelect2();
                this.curricula = {};
            },
            initSelect2(){
                $("#curricula").select2({
                    dropdownParent: $("#curricula").parent(),
                    allowClear: false
                }).on('select2:select', function (e) {
                    this.form.curriculum_id = e.params.data.id;
                }.bind(this)) //make setEnabling accessible!
                    .val(this.form.curriculum_id).trigger('change'); //set value
            },
            close(){
                this.$modal.hide('move-terminal-objective-modal');
            },
        }
    }
</script>
