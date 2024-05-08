<template>
    <modal
        id="note-modal"
        name="note-modal"
        height="auto"
        :adaptive=true
        :resizable=true
        draggable=".draggable"
        @before-open="beforeOpen"
        style="z-index: 1200"
    >
        <div class="card h-100 mb-0" style="max-height: 100svh;">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.notes') }}
                </h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool draggable">
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button"
                            class="btn btn-tool"
                            data-widget="remove"
                            @click="close()"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="card-body overflow-auto p-0">
                <notes
                    :notable_type="notable_type"
                    :notable_id="notable_id"
                    :show_tabs="show_tabs"
                    :users="users"
                ></notes>    
            </div>

            <div class="card-footer">
                <span class="pull-right">
                    <button type="button"
                            class="btn btn-default"
                            data-widget="remove"
                            @click="close()"
                    >
                        {{ trans('global.close') }}
                    </button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
const Notes =
    () => import('../note/Notes');
    //import Notes from '../note/Notes'

export default {
    data() {
        return {
            notable_type: { type: String },
            notable_id: { type: [Number, Array] },
            show_tabs: { type: Boolean, default: true },
            users: { type: Object, default: undefined },
        }
    },
    methods: {
        beforeOpen(event) {
            this.notable_type = event.params.notable_type;
            this.notable_id = event.params.notable_id;
            this.show_tabs = event.params.show_tabs;
            this.users = event.params.users;
        },
        close() {
            this.$modal.hide('note-modal');
        },
    },
    components: {
        Notes,
    }
}
</script>
