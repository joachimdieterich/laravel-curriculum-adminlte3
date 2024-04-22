<template>
  <div class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <b v-if="this.method === 'post'" class="modal-title">
            {{ trans('global.kanban.create') }}
          </b>
          <b v-else>
            {{ trans('global.kanban.edit') }}
          </b>
          <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0">
          <div class="card mb-0">
            <div class="card-header border-bottom"
                 data-card-widget="collapse">
              <h5 class="card-title">
                Allgemein
              </h5>
            </div>
            <!-- /.card-header -->
            <div class="card-body pb-0">
              <div class="form-group">
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    v-model.trim="form.title"
                    :placeholder="trans('global.kanbanItem.fields.title')"
                    required
                />
                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
              </div>
              <div class="form-group">
                 <textarea
                     id="description"
                     name="description"
                     :placeholder="trans('global.kanbanItem.fields.description')"
                     class="form-control description "
                     v-model.trim="form.description"
                 ></textarea>
                <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
              </div>
            </div>
            <!-- /.card-body -->
          </div>

          <div class="card mb-0">
            <div class="card-header border-bottom"
                 data-card-widget="collapse">
              <h5 class="card-title">
                Darstellung
              </h5>
            </div>
            <!-- /.card-header -->
            <div class="card-body pb-0">
              <span
                  class="pull-left"
                  :style="{borderColor: textColor }">
                <color-picker-input
                    v-model="form.color">
                </color-picker-input>
            </span>
              <MediumForm
                  class="pull-right"
                  :form="form"
                  :id="component_id"
                  :medium_id="form.medium_id"
                  accept="image/*"
              />
            </div>
            <!-- /.card-body -->
          </div>

          <div class="card mb-0">
            <div class="card-header  border-bottom"
                 data-card-widget="collapse">
              <h5 class="card-title">
                Berechtigungen
              </h5>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <span class="custom-control custom-switch custom-switch-on-green">
                <input
                    v-model="form.commentable"
                    type="checkbox"
                    class="custom-control-input pt-1 "
                    :id="'commentable_'+ form.id">
                <label class="custom-control-label font-weight-light"
                       :for="'commentable_'+ form.id">
                    {{ trans('global.commentable') }}
                </label>
              </span>
              <span class="custom-control custom-switch custom-switch-on-green">
                <input
                    v-model="form.auto_refresh"
                    type="checkbox"
                    class="custom-control-input pt-1 "
                    :id="'auto_refresh_'+ form.id">
                <label class="custom-control-label font-weight-light"
                       :for="'auto_refresh_'+ form.id">
                    {{ trans('global.auto_refresh') }}
                </label>
              </span>
              <span class="custom-control custom-switch custom-switch-on-green">
                <input
                    v-model="form.only_edit_owned_items"
                    type="checkbox"
                    class="custom-control-input pt-1 "
                    :id="'only_edit_owned_items_'+ form.id">
                <label class="custom-control-label font-weight-light"
                       :for="'only_edit_owned_items_'+ form.id">
                    {{ trans('global.kanban.only_edit_owned_items') }}
                </label>
                              </span>
              <span class="custom-control custom-switch custom-switch-on-green">
                <input
                    v-model="form.allow_copy"
                    type="checkbox"
                    class="custom-control-input pt-1 "
                    :id="'allow_copy_'+ form.id">
                <label class="custom-control-label font-weight-light"
                       :for="'allow_copy_'+ form.id">
                    {{ trans('global.kanban.allow_copy') }}
                </label>
              </span>
            </div>
            <!-- /.card-body -->
          </div>


        </div>
        <div class="modal-footer justify-content-between">
          <button type="button"
                  class="btn btn-default"
                  data-dismiss="modal">
            {{ trans('global.cancel') }}
          </button>
          <button type="button"
                  class="btn btn-primary"
                  data-dismiss="modal"
                  @click="submit()">
            {{ trans('global.save') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import MediumForm from "../media/MediumForm";
import Form from "form-backend-validation";

export default {
    name: 'KanbanCreate',
    components: {MediumForm},
    props: {
        kanban: {},
        method: ''
    },
    data() {
        return {
            component_id: this._uid,
            requestUrl: '/kanbans',
            form: new Form({
                'id': '',
                'title':  '',
                'description':  '',
                'color':'#27AF60',
                'medium_id': null,
                'commentable': true,
                'auto_refresh': false,
                'only_edit_owned_items': false,
                'allow_copy': true,
            }),
        };
    },
    watch: {
        kanban: function(newVal, oldVal) {
            //console.log(newVal);
            this.form.id = newVal.id;
            this.form.title = newVal.title;
            this.form.description = this.htmlToText(newVal.description);
            this.form.color = newVal.color;
            this.form.medium_id = newVal.medium_id;
            this.form.commentable = newVal.commentable;
            this.form.auto_refresh = newVal.auto_refresh;
            this.form.only_edit_owned_items = newVal.only_edit_owned_items;
            this.form.allow_copy = newVal.allow_copy;
        },
        method: function (newVal, oldVal) {
            if (newVal == 'post') {
                this.form.reset();
            }
        }
    },
    computed:{
        textColor: function(){
            return this.$textcolor(this.form.color, '#333333');
        }
    },
    methods: {
        submit() {
            let method = this.method.toLowerCase();

            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a task
                        this.$eventHub.$emit("kanban-updated", res.data.kanban);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });

            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        window.location = res.data.message;
                        //this.$eventHub.$emit("kanban-added", res.data.message);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error)
                    });
            }
        },
    },
    mounted() {
        // Set eventlistener for Media
        this.$eventHub.$on('addMedia', (e) => {
            if (this.component_id == e.id) {
                this.form.medium_id = e.selectedMediumId;
                if ( Array.isArray(this.form.medium_id))  {
                    this.form.medium_id = this.form.medium_id[0]; //Hack to get existing files working.
                }
            }
        });
    }

}
</script>
