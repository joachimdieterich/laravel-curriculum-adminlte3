<template>
  <div class="modal fade" :id="'modal-' + css">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"
        :class="'bg-' + css">
          <h4 class="modal-title">{{ this.title }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>{{ this.text }}</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button"
                  class="btn btn-default"
                  data-dismiss="modal">
              {{ this.cancel_label }}
          </button>
          <button type="button"
                  class="btn btn-primary"
                  data-dismiss="modal"
                  @click="$emit('ok')">
              {{ this.ok_label }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
    name: 'Modal',
    props: {
        css: {
            type: String,
            default: 'default'
        },
        title: {
            type: String,
            default: ''
        },
        text: {
            type: String,
            default: ''
        },
        'cancel': true,
        cancel_label: {
            type: String,
            default:  window.trans.global.cancel
        },
        ok_label: {
            type: String,
            default: window.trans.global.save
        },
    },
}


</script>
