<template>
    <modal
        id="note-show-modal"
        name="note-show-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        @before-open="beforeOpen"
        style="z-index: 1300"
        :styles="{ transform: 'translateX(10px)' }"
    >
        <div class="card mb-0" style="max-height: 90svh;">
            <div class="card-header">
                <h3 class="card-title">
                    {{ note.title }}
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

            <div v-dompurify-html="note.content"
                class="card-body overflow-auto"
            ></div>

            <div class="card-footer">
                <span class="d-flex pull-right">
                    <button type="button" class="btn btn-default" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>
<script>
export default {
    name: 'NoteShowModal',
    data() {
        return {
            note: {},
        }
    },
    methods: {
        beforeOpen(event) {
            this.note = event.params.note;
        },
        close() {
            this.$modal.hide('note-show-modal');
            this.note = {};
        },
    },
    computed: {
        windowHeight() {
            return window.innerHeight;
        }
    },
}
</script>
