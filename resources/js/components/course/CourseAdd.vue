<template>
  <div class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <b class="modal-title">
            {{ trans('global.course.create') }}
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
                <div class="form-group pt-2">
                    <select name="curricula"
                            id="curricula"
                            class="form-control select2 "
                            style="width:100%;"
                    ></select>
                </div>
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
    </div>
</template>
<script>

import Form from "form-backend-validation";

export default {
    name: 'CourseAdd',
    props: {
        group: {}
    },
    data() {
        return {
            component_id: this._uid,
            requestUrl: '/curricula',
            curricula: {},
            form: new Form({
                'curriculum_id': '',
                'enrollment_list': {},

            }),
        };
    },

    methods: {
        submit() {
            axios.post(
                    this.requestUrl + '/enrol',
                    {
                        'enrollment_list' : {
                            0: {
                                'group_id' : this.group.id,
                                'curriculum_id': {
                                    0 : this.form.curriculum_id
                                }
                            }
                        }
                    }
                )
                .then(res => {
                    this.$eventHub.emit("course-updated", res.data.message);
                })
                .catch(error => { // Handle the error returned from our request
                    console.log(error)
                });
        },
        initSelect2() {
            $("#curricula").select2({
                allowClear: false,
                ajax: {
                    url: this.requestUrl,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                },
            }).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            }).on('select2:select', function (e) {
                this.form.curriculum_id =  e.params.data.id;
            }.bind(this));
        },
    },
    mounted() {
        this.$eventHub.on('load_curricula', () => {
            this.initSelect2();
        });
    },

}
</script>
