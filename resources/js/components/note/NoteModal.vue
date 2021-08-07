<template>
    <modal
        id="note-modal"
        name="note-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        style="z-index: 1200">
        <div class="card"
             style="margin-bottom: 0 !important">
            <div class="card-header">
                 <h3 class="card-title">
                    {{ trans('global.notes') }}
                 </h3>

                 <div class="card-tools">
                     <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                     </button>
                     <button type="button"
                             class="btn btn-tool"
                             data-widget="remove"
                             @click="close()">
                        <i class="fa fa-times"></i>
                     </button>
                 </div>
            </div>

            <notes :notable_type="notable_type"
                   :notable_id="notable_id"
                   :show_tabs="show_tabs"></notes>

            <div class="card-footer">
                <span class="pull-right">
                     <button type="button"
                             class="btn btn-default"
                             data-widget="remove"
                             @click="close()">
                         {{ trans('global.close') }}
                     </button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    import Notes from '../note/Notes'

    export default {
        data() {
            return {
                notable_type: { type: Boolean, default: false },
                notable_id: { type: Boolean, default: false },
                show_tabs: { type: Boolean, default: true }
            }
        },
        methods: {
            beforeOpen(event) {
                this.notable_type = event.params.notable_type;
                this.notable_id = event.params.notable_id;
                this.show_tabs = event.params.show_tabs;
            },
            close(){
                this.$modal.hide('note-modal');
            },
        },
        components: {
            Notes,
        }
    }
</script>
