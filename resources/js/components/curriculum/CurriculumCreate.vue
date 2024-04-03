<template>
  <div class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <b  v-if="form.id == ''"
              class="modal-title">
            {{ trans('global.curriculum.create') }}
          </b>
            <b  v-else
                class="modal-title">
                {{ trans('global.curriculum.edit') }}
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
            <div class="card-body pb-0">
              <div class="input-group pb-1">
                  <color-picker-input
                      class="input-group-prepend"
                      v-model="form.color">
                  </color-picker-input>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control ml-3"
                    style="height:42px"
                    v-model.trim="form.title"
                    :placeholder="trans('global.curriculum.fields.title')"
                    required
                />
                <p class="help-block" v-if="form.errors?.title" v-text="form.errors?.title[0]"></p>
              </div>

                <div class="form-group ">
                    <label for="description">
                        {{ trans('global.curriculum.fields.description') }}
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        :placeholder="trans('global.curriculum.fields.description')"
                        class="form-control description my-editor "
                        v-model="form.description"
                    ></textarea>
                </div>

                  <div class="form-group ">
                      <label for="author">
                          {{ trans('global.curriculum.fields.author') }}
                      </label>
                      <input
                          type="text"
                          id="author"
                          name="author"
                          class="form-control"
                          required
                          v-model.trim="form.author"
                      />
                      <p class="help-block" v-if="form.errors?.author" v-text="form.errors?.author[0]"></p>
                  </div>

                  <div class="form-group ">
                      <label for="publisher">
                          {{ trans('global.curriculum.fields.publisher') }}
                      </label>
                      <input
                          type="text"
                          id="publisher"
                          name="publisher"
                          class="form-control"
                          required
                          v-model.trim="form.publisher"
                      />
                      <p class="help-block" v-if="form.errors?.publisher" v-text="form.errors?.publisher[0]"></p>
                  </div>

                <div class="form-group ">
                    <label for="publisher">
                        {{ trans('global.curriculum.fields.city') }}
                    </label>
                    <input
                        type="text"
                        id="publisher"
                        name="publisher"
                        class="form-control"
                        required
                        v-model.trim="form.city"
                    />
                    <p class="help-block" v-if="form.errors?.city" v-text="form.errors?.city[0]"></p>
                </div>

                <div class="form-group pt-2">
                    <label for="author">
                        {{ trans('global.curriculum.fields.date') }}
                    </label>
                    <date-picker
                        v-model="form.date" style="width:100%;"
                        valueType="YYYY-MM-DD HH:mm:ss"
                        :placeholder="trans('global.curriculum.fields.date')"
                    ></date-picker>
                </div>

<!--                File-Picker-->
                <div class="form-group ">
                    <Select2
                        :id="'grade_id'"
                        model="grade"
                        :selected="this.form.grade_id"
                        url="/grades"
                        :placeholder="trans('global.pleaseSelect')"
                        @selectedValue="(id) => this.form.grade_id = id"
                    ></Select2>
                </div>
                <div class="form-group ">
                    <Select2
                        :id="'subject_id'"
                        model="subject"
                        :selected="this.form.grade_id"
                        url="/subjects"
                        :placeholder="trans('global.pleaseSelect')"
                        @selectedValue="(id) => this.form.subject_id = id"
                    ></Select2>
                </div>
<!--                variants-->
                <div class="form-group ">
                    <Select2
                        :id="'organization_type_id'"
                        model="organizationtype"
                        :selected="this.form.organization_type_id"
                        url="/organizationtypes"
                        :placeholder="trans('global.pleaseSelect')"
                        @selectedValue="(id) => this.form.organization_type_id = id"
                    ></Select2>
                </div>
                <div class="form-group ">
                    <label for="type_id">
                        {{ trans('global.curriculumtype.title_singular') }}
                    </label>
                    <select
                        id="type_id"
                        v-model="form.type_id"
                        class="form-control select2"
                        style="width:100%;">
                        <option v-for="type in types"
                                :id="type.id"
                                :value="type.title">
                            {{ trans('global.' + type.title + '.title_singular') }}
                        </option>
                    </select>
                </div>

                <div class="form-group ">
                    <label for="country_id">
                        {{ trans('global.country.title_singular') }}
                    </label>
                    <select
                        id="country_id"
                        v-model="form.country_id"
                        class="form-control select2"
                        style="width:100%;">
                        <option v-for="item in countries"
                                :id="item.alpha2"
                                :value="item.lang_de">
                            {{ item.lang_de }}
                        </option>
                    </select>
                </div>

                <div class="form-group ">
                    <label for="state_id">
                        {{ trans('global.state.title_singular') }}
                    </label>
                    <select
                        id="state_id"
                        v-model="form.state_id"
                        class="form-control select2"
                        style="width:100%;">
                        <option v-for="item in states"
                                :id="item.code"
                                :value="item.lang_de">
                            {{ item.lang_de }}
                        </option>
                    </select>
                </div>

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
import Form from "form-backend-validation";
import DatePicker from 'vue2-datepicker';
const Select2 =
    () => import('../forms/Select2');

export default {
    name: 'curriculumCreate',
    components: {
        DatePicker,
        Select2
    },
    props: {
        curriculum: {},
        method: ''
    },
    data() {
        return {
            component_id: this._uid,
            requestUrl: '/curricula',
            form: new Form({
                'id':'',
                'title': '',
                'description': '',
                'author': '',
                'publisher': '',
                'city': '',
                'date': '',
                'color': '#F2C511',
                'grade_id': 1,
                'subject_id': 1,
                'organization_type_id': 1,
                'state_id': 'DE-RP',
                'country_id': 'DE',
                'medium_id': null,
                'owner_id': '',
                'type_id': 4,
            }),
            types:{},
            grades:{},
            subjects:{},
            organizationTypes:{},
            countries:{},
            states:{},
        };
    },
    watch: {
        curriculum: function(newVal, oldVal) {
            //console.log(newVal);
            this.form.id = newVal.id;
            this.form.title = newVal.title;
            this.form.description = this.decodeHtml(newVal.description);
            this.form.author = newVal.author;
            this.form.publisher = newVal.publisher;
            this.form.city = newVal.city;
            this.form.date = newVal.date;
            this.form.color = newVal.color;
            this.form.grade_id = newVal.grade_id;
            this.form.subject_id = newVal.subject_id;
            this.form.organization_type_id = newVal.organization_type_id;
            this.form.state_id = newVal.state_id;
            this.form.country_id = newVal.country_id;
            this.form.medium_id = newVal.medium_id;
            this.form.owner_id = newVal.owner_id;
            this.form.type_id = newVal.type_id;
            this.method= 'patch';

            this.$initTinyMCE([
                "autolink link"
            ] );
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
            this.form.description = tinyMCE.get('description').getContent();
            if (method === 'patch') {
                axios.patch(this.requestUrl + '/' + this.form.id, this.form)
                    .then(res => { // Tell the parent component we've updated a task
                        this.$eventHub.$emit("curriculum-updated", res.data);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error);
                    });
            } else {
                axios.post(this.requestUrl, this.form)
                    .then(res => {
                        this.$eventHub.$emit("curriculum-added", res.data.curriculum);
                    })
                    .catch(error => { // Handle the error returned from our request
                        console.log(error)
                    });
            }
        },
        loadStates(){
            axios.get('/countries/'+ this.form.country_id)
                .then(response => {
                    this.states = response.data;

                    $('#state_id').select2({
                        dropdownParent: $('#state_id').parent(),
                    }).on("select2:select", function(e){
                        this.form.state_id = e.params.data.element.id
                    }.bind(this))
                        .val(this.states[this.form.state_id])
                        .trigger('change');
                })
                .catch(e => {
                    console.log(e);
                });
        },
        decodeHtml(html) {
            var txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }
    },
    mounted() {

        axios.get('/curricula/types')
            .then(response => {
                this.types = response.data;

                $('#type_id').select2({
                    dropdownParent: $('#type_id').parent(),
                }).on("select2:select", function(e){
                    this.form.type_id = e.params.data.element.id
                }.bind(this))
                .val(this.types['global'])
                .trigger('change');
            })
            .catch(e => {
                console.log(e);
            });

        axios.get('/countries')
            .then(response => {
                this.countries = response.data;

                $('#country_id').select2({
                    dropdownParent: $('#country_id').parent(),
                }).on("select2:select", function(e){
                    this.form.country_id = e.params.data.element.id
                    this.loadStates();
                }.bind(this))
                    .val(this.countries[this.form.country_id])
                    .trigger('change');
            })
            .catch(e => {
                console.log(e);
            });

        this.$initTinyMCE([
            "autolink link"
        ] );
    },
}
</script>
