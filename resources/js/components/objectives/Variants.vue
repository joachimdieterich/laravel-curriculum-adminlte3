<template>
    <div >

        <draggable
            class="row px-1"
            :disabled='!enableDraggable'
            v-model="definitions"
            @start="drag=true"
            @end="handleVariantMoved">
            <!-- no variantdefinitions in curriculum OR no variants on objective -->
            <div v-if="model.curriculum.variants === null || (!enableDraggable && model.variants.length === 0)"
                 class="col-12 pb-2">
                <div v-if="field === 'description'"
                     v-html="model.description"></div>
                <div v-else
                     v-html="model.title"></div>
            </div>

            <!-- with variants -->
            <div v-else
                 v-for="variant_definition in definitions"
                 :class="width_css"
                 class="px-2">
                <div style="height:100%"
                     :class="css_form">
                    <i v-if="field !== 'description' && variant_definition.id !== 0"
                       v-can="'curriculum_edit'"
                       class="fa fa-pencil-alt pointer pull-right"
                       @click="togglEdit(variant_definition.id)"></i>
                    <p class="text-bold" v-if="showTitle">
                        {{ variant_definition.title }}
                    </p>
                    <div v-if="variant_definition.id === 0">
                        <div v-if="field === 'description' "
                             v-html="model.description"></div>
                        <div v-else v-html="model.title"></div>
                    </div>
                    <div v-else>
                    <span v-if="filterVariant(variant_definition.id).length !== 0">
                        <div v-if="filterVariant(variant_definition.id)[0][field] != '' "
                             style="height:100%">
                            <span v-html="filterVariant(variant_definition.id)[0][field]"></span>
                        </div>
                    </span>

                    </div>

                </div>

            </div>
        </draggable>

        <div v-if="edit"
             class="row pt-2 ">
            <div class="col-12">
                <div >
                    <div class="form-group "
                         :class="form.errors.title ? 'has-error' : ''">
                        <label for="title">{{ trans('global.terminalObjective.fields.title') }} *</label>
                        <textarea
                            id="title"
                            name="title"
                            class="form-control description my-editor"
                            v-model="form.title"
                        ></textarea>
                        <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
                    </div>
                    <div class="form-group "
                         :class="form.errors.description ? 'has-error' : ''">
                        <label for="description">{{ trans('global.terminalObjective.fields.description') }}</label>
                        <textarea
                            id="description"
                            name="description"
                            class="form-control description my-editor"
                            v-model="form.description"
                        ></textarea>
                        <p class="help-block" v-if="form.errors.description" v-text="form.errors.description[0]"></p>
                    </div>
                    <span class="">
                         <button type="button" class="btn btn-info" @click="edit = !edit;">{{ trans('global.cancel') }}</button>
                         <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                    </span>
                </div>
            </div>
        </div>

    </div>

</template>
<script>
import Form from 'form-backend-validation';
import draggable from "vuedraggable";


export default {
  name: 'variants',
  props: {
        model: {},
        referenceable_type: String,
        referenceable_id: Number,
        variant_order: {},
        field: {
            type: String,
            default:'title'
        },
        showTitle:{
            type: Boolean,
            default:true,
        },
        css_size: {
            type: String,
            default:'col-md-4 col-sm-12'
        },
        css_form: {
            type: String,
            default: 'callout callout-primary'
        }
  },
  data() {
        return {
            form: new Form({
                'id': '',
                'title': '',
                'description': '',
                'referenceable_type': '',
                'referenceable_id': '',
                'variant_definition_id': '',
            }),
            variants:{},
            definitions: null,
            edit: false,
        }
  },


  methods: {
      loadVariantDefinitions: function () {
          axios.get('/curricula/' + this.model.curriculum.id + '/variantDefinitions')
              .then(response => {
              this.definitions = response.data.definitions;
                  MathJax.startup.defaultReady();
          }).catch(e => {
              console.log(e);
          });
      },
      handleVariantMoved() {
          axios.put("/curricula/"+this.model.curriculum.id+"/variantDefinitions", {variants: this.definitions.map(s=>s.id)})
              .catch(err => {
                  console.log(err.response);
                  alert(err.response.statusText);
              });
      },


      togglEdit(id) {
          this.edit = !this.edit;
          let variant = this.filterVariant(id)[0];
          if (typeof variant != 'undefined') {
              this.form.id = variant.id;
              this.form.title = variant.title;
              this.form.description = variant.description;
          }
          this.form.variant_definition_id = id;

          this.$nextTick(() => {
              this.$initTinyMCE();
          });

      },
      filterVariant(id) {
          let variant =  this.variants.filter(
              v => v.variant_definition_id === id
          );

          return variant;
      },
      filterVariantDefinition(id) {
          let definition = this.filterVariant(id)[0]['definition']

          return definition;
      },
      submit() {
          this.form.title = tinyMCE.get('title').getContent();
          this.form.description = tinyMCE.get('description').getContent();
          this.form.referenceable_type = this.referenceable_type;
          this.form.referenceable_id = this.referenceable_id;

          if (this.form.id !== '') {
              let id = this.form.id;
              this.form.patch( '/variants/' + id)
                  .then(response => {
                      const variantIndex =  this.variants.findIndex(
                          v => v.variant_definition_id === id
                      );

                      this.variants[variantIndex].title = response.variant.title;
                      this.variants[variantIndex].description = response.variant.description;
                  })
          } else {
              this.form.post('/variants/')
                  .then(response =>  {
                      this.variants.push(response.variant);
                  });
          }
          this.edit = !this.edit;
      },

  },
    mounted() {
        this.loadVariantDefinitions();
        this.variants = this.model.variants;
    },
    computed: {
        enableDraggable(){
            return window.Laravel.permissions.indexOf('curriculum_edit') !== -1; //v-can="curriculum_edit"
        },
      width_css() {
          if (this.field === 'description'){
              return 'col-12';
          }

          switch(this.variant_order.length) {
              case 2:
                  return 'col-md-6 col-sm-12';
                  break;
              case 3:
                  return 'col-md-4 col-sm-12';
                  break;
              case 4:
                  return 'col-md-3 col-sm-12';
                  break;
              case 5:
              case 6:
                  return 'col-md-2 col-sm-12';
                  break;

              default: return 'col-md-4 col-sm-12';
                  break;
          }
      }
    },
    components: {
        draggable,
    }
}
</script>
