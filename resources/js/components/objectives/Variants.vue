<template>
    <div class="row px-1">
        <!-- no variants -->
        <div v-if="model.curriculum.variants === null || model.variants.length === 0"
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
                <p class="text-bold">
                    {{ variant_definition.title }}
                </p>
                <div v-if="variant_definition.id === 0">
                    <div v-if="field === 'description'"
                         v-html="model.description"></div>
                    <div v-else v-html="model.title"></div>
                </div>
                <div v-else>
                    <span v-if="filterVariant(variant_definition.id).length !== 0">
                        <div v-if="filterVariant(variant_definition.id)[0][field] != ''"
                             style="height:100%">
                            <span v-html="filterVariant(variant_definition.id)[0][field]"></span>
                        </div>
                    </span>
                    <i v-if="field !== 'description'"
                       class="fa fa-pencil-alt pointer pull-right"
                        @click="togglEdit(variant_definition.id)"></i>
                </div>

            </div>

        </div>
        <div class="px-2 pt-2">
            <div :class="css_form">+ Variante hinzufügen
                <div v-if="edit">
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
                </div>
            </div>
        </div>

    </div>

</template>
<script>
import Form from 'form-backend-validation';

export default {
  name: 'variants',
  props: {
        model: {},
        variant_order: {},
        field: 'title',
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
            definitions: null,
            edit: false,
        }
  },


  methods: {
      loadVariants: function () {
          axios.get('/curricula/' + this.model.curriculum.id + '/variantDefinitions').then(response => {
              this.definitions = response.data.definitions;
          }).catch(e => {
              console.log(e);
          });
      },
      togglEdit(id) {
          let variant = this.filterVariant(id);
          this.form.title = variant.title;
          this.form.description = variant.description;
          this.referenceable_type = '';
          this.referenceable_id = this.model.id;
          this.edit = !this.edit;
          this.$initTinyMCE();

      },
      filterVariant(id) {
          let variant =  this.model.variants.filter(
              v => v.variant_definition_id === id
          );

          return variant;
      },
      filterVariantDefinition(id) {
          let definition = this.filterVariant(id)[0]['definition']
          //console.log(definition);
          return definition;
      },

  },
    mounted() {
        this.loadVariants();
    },
    computed: {
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
    }
}
</script>
